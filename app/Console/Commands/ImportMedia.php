<?php

namespace App\Console\Commands;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\FileFormat;
use App\Models\Identification;
use App\Models\IdentificationType;
use App\Models\Image;
use App\Models\Library;
use App\Models\Media;
use App\Models\Publisher;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Process\Process;
use ZipArchive;

class ImportMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the import disk for new media and import';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $storagePath = storage_path('app/import');
        // $files = File::allFiles($storagePath);
        $files = File::allFiles($storagePath);
        // dump($files);

        foreach ($files as $key => $file) {
            $toProcess = [
                'realPath' => $file->getRealPath(),
                'relativePath' => str_replace($storagePath, '.', $file->getRealPath()),
                'filename' => $file->getFilename(),
                'extension' => $file->getExtension(),
                'size' => $file->getSize(),
            ];

            // dump($toProcess);

            // Process EPub file
            $this->_processEpub($toProcess);

            // Process PDF file
            $this->_processPDF($toProcess);

            // Unset the file from the array to free up memory
            unset($files[$key]);
        }

        return 0;
    }

    private function _processPDF(array $toProcess)
    {
        $metaData = collect();

        if ($toProcess['extension'] == 'pdf') {
            $this->line($toProcess['filename']);
            $this->newLine();

            $textPath = str_replace(['.pdf', '.PDF'], '.txt', $toProcess['realPath']);

            if (!File::exists($textPath)) {
                $process = new Process([
                    'pdftotext',
                    $toProcess['realPath'],
                    $textPath
                ]);
                $process->run();

                dump($process->getErrorOutput());
            }

            $fileContents = File::get($textPath);

            // dump($fileContents);

            $isbns = [
                'book' => [],
                'ebook' => [],
            ];
            $this->_findISBNs($isbns, $fileContents);
            dump($isbns);

            $this->_fetchGoogleMetadata($toProcess, $isbns, $metaData);

            dump($metaData);

            $this->_processMetaData($metaData, $toProcess);

            $this->line("--------------------------------------------------------------------------------------------------");
            $this->newLine();
        }
    }

    private function _processEpub(array $toProcess)
    {
        $metaData = collect();

        if ($toProcess['extension'] == 'epub') {
            $this->line($toProcess['filename']);
            $this->newLine();

            // open file
            $zip = new ZipArchive();
            if (!@$zip->open($toProcess['realPath'])) {
                throw new Exception('Failed to read epub file');
            }

            $isbns = [
                'book' => [],
                'ebook' => [],
            ];
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);

                if (
                    str_contains($stat['name'], '.html') ||
                    str_contains($stat['name'], '.xhtml') ||
                    str_contains($stat['name'], '.htm')
                ) {

                    $fileContents = $zip->getFromName($stat['name']);

                    // if ($toProcess['filename'] == 'Ottolenghi Simple - Yotam Ottolenghi.epub' && $stat['name'] == 'OEBPS/copy.xhtml')
                    //     $this->info($fileContents);

                    $this->_findISBNs($isbns, $fileContents);
                }
            }
            dump($isbns);

            $this->_fetchGoogleMetadata($toProcess, $isbns, $metaData);

            dump($metaData);

            $this->_processMetaData($metaData, $toProcess);

            $this->line("--------------------------------------------------------------------------------------------------");
            $this->newLine();
        }
    }

    private function _processMetaData(Collection $metaData, array $toProcess)
    {
        if (isset($metaData['authors']) && count($metaData['authors'])) {
            // $this->error(print_r($metaData['authors'], true));

            try {
                DB::beginTransaction();

                // Check if the default library exists and create it if not
                $defaultLibrary = $this->_fetchOrCreateDefaultLibrary();

                // Check if the publisher exists and create it if not
                $publisher = $this->_fetchOrCreatePublisher($metaData['publisher']);

                // Check if the book exists and create it if not
                $book = $this->_fetchOrCreateBook($metaData, $defaultLibrary, $publisher);

                // Check and Attach identifiers to the book
                $identifiers = $this->_fetchOrCreateIdentifiers($book, $metaData['identifiers']);

                // Check if the authors exist and create it if not
                $authors = $this->_fetchOrCreateAuthors($metaData['authors']);
                // Attach authors to the book
                $book->authors()->sync($authors->pluck('id'));

                // Check if the categories exist and create it if not
                $categories = $this->_fetchOrCreateCategories($metaData['categories']);
                // Attach categories to the book
                $book->categories()->sync($categories->pluck('id'));

                $path = $this->_getTargetPath($toProcess, $metaData);

                $images = $this->_processImages($book, $path, $metaData);

                $media = $this->_processMedia($book, $toProcess, $path);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();

                Cache::forget('hasDefaultLibrary');
                Cache::tags('publishers')->flush();

                $this->error('ERROR: ' . $e->getMessage());
                dd('done');
            }
        }
    }

    private function _processImages(Book $book, array $path, Collection $metaData)
    {
        foreach (['thumbnail', 'small', 'medium'] as $key => $imageFormat) {
            if (isset($metaData['images'][$imageFormat])) {
                $response = Http::get($metaData['images'][$imageFormat]);

                if ($response->successful()) {
                    $mimeType = $response->header('content-type');

                    $ext = false;
                    if ($mimeType === 'image/jpeg') {
                        $ext = 'jpg';
                    } elseif ($mimeType === 'image/png') {
                        $ext = 'png';
                    }

                    if (!$ext)
                        continue;

                    $filename = 'cover_' . $imageFormat . '.' . $ext;
                    $fullPath = $path['path'] . '/' . $filename;

                    $this->newLine();
                    $this->info($fullPath);
                    $this->newLine();

                    File::put($fullPath, $response->body());

                    Image::create([
                        'book_id' => $book->id,
                        'format' => $imageFormat,
                        'path' => str_replace(storage_path('app/public/books') . '/', '', $fullPath),
                        'size' => File::size($fullPath),
                    ]);
                }
            }
        }
    }

    private function _getTargetPath(array $toProcess, Collection $metaData)
    {
        $sanitizedAuthors = filter_filename(implode(' & ', $metaData['authors']));
        $sanitizedTitle = filter_filename($metaData['title']);

        $path = storage_path('app/public/books') . '/' . $sanitizedAuthors . '/' . $sanitizedTitle;

        $this->error($path);
        File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);

        $standardizedFilename = $sanitizedTitle . ' - ' . $sanitizedAuthors . ' [' . $metaData['language'] . '] [' . implode(',', collect($metaData['identifiers'])->pluck('identifier')->values()->toArray()) . '].' . $toProcess['extension'];

        $fullPath = $path . '/' . $standardizedFilename;

        if (File::exists($fullPath)) {
            $i = 1;
            while (File::exists($fullPath)) {
                $standardizedFilename = $sanitizedTitle . ' - ' . $sanitizedAuthors . ' [' . $metaData['language'] . '] [' . implode(',', collect($metaData['identifiers'])->pluck('identifier')->values()->toArray()) . '] (' . $i . ').' . $toProcess['extension'];
                $fullPath = $path . '/' . $standardizedFilename;
            }
        }

        return [
            'path' => $path,
            'fullPath' => $fullPath,
        ];
    }

    private function _processMedia(Book $book, array $toProcess, array $path)
    {
        dump($toProcess);

        $this->error($path['fullPath']);

        File::copy($toProcess['realPath'], $path['fullPath']);

        // Check if the File Format exists and create if not
        $fileFormat = $this->_fetchOrCreateFileFormat($toProcess['extension']);

        Media::create([
            'book_id' => $book->id,
            'file_format_id' => $fileFormat->id,
            'path' => str_replace(storage_path('app/public/books') . '/', '', $path['fullPath']),
            'size' => $toProcess['size'],
        ]);
    }

    private function _fetchOrCreateFileFormat(string $extensionString)
    {
        $fileFormat = FileFormat::where('name', $extensionString)->first();
        if (!$fileFormat) {
            $fileFormat = FileFormat::create([
                'name' => $extensionString
            ]);
        }

        return $fileFormat;
    }

    private function _fetchOrCreateIdentifiers(Book $book, array $identifiers)
    {
        foreach ($identifiers as $key => $identifier) {
            $identity = Identification::whereHas('identificationType', function (Builder $query) use ($identifier) {
                $query->where('name', $identifier['type']);
            })
                ->where('value', $identifier['identifier'])
                ->first();

            if (!$identity) {
                // Check if the Identification Type exists and create if not
                $identificationType = $this->_fetchOrCreateIdentificationType($identifier['type']);

                Identification::create([
                    'book_id' => $book->id,
                    'identification_type_id' => $identificationType->id,
                    'value' => $identifier['identifier'],
                ]);
            }
        }
    }

    private function _fetchOrCreateIdentificationType(string $identificationTypeString)
    {
        $identificationType = IdentificationType::where('name', $identificationTypeString)->first();
        if (!$identificationType) {
            $identificationType = IdentificationType::create([
                'name' => $identificationTypeString
            ]);
        }

        return $identificationType;
    }

    private function _fetchOrCreateBook(Collection $metaData, Library $defaultLibrary, Publisher $publisher)
    {
        foreach ($metaData['identifiers'] as $key => $identifier) {
            $identifier = Identification::whereHas('identificationType', function (Builder $query) use ($identifier) {
                $query->where('name', $identifier['type']);
            })
                ->where('value', $identifier['identifier'])
                ->first();

            if ($identifier) {
                return $identifier->book()->first();
            }
        }

        // Create the book
        return Book::create([
            'library_id' => $defaultLibrary->id,
            'title' => $metaData['title'],
            'sub_title' => $metaData['sub_title'],
            'description' => $metaData['description'],
            'edition' => $metaData['edition'],
            'language' => $metaData['language'],
            'page_count' => $metaData['page_count'],
            'publisher_id' => $publisher->id,
            'publish_date' => $metaData['publish_date'],
        ]);
    }

    private function _fetchOrCreateDefaultLibrary()
    {
        $defaultLibrary = Cache::remember('hasDefaultLibrary', 3600, function () {
            return Library::where('name', 'Unsorted')->first();
        });

        if (!$defaultLibrary) {
            DB::transaction(function () use (&$defaultLibrary) {
                $defaultLibrary = Library::create([
                    'name' => 'Unsorted'
                ]);

                Cache::forget('hasDefaultLibrary');
            });
        }

        return $defaultLibrary;
    }

    private function _fetchOrCreatePublisher(string $publisherString)
    {
        $key = 'publisher_' . md5($publisherString);

        $publisher = Cache::tags('publishers')->remember($key, 300, function () use ($publisherString) {
            return Publisher::where('name', $publisherString)->first();
        });

        if (!$publisher) {
            DB::transaction(function () use (&$publisher, $publisherString, $key) {
                $publisher = Publisher::create([
                    'name' => $publisherString
                ]);

                Cache::forget($key);
            });
        }

        return $publisher;
    }

    private function _fetchOrCreateAuthors(array $authorsArray)
    {
        $authors = collect();
        foreach ($authorsArray as $key => $authorString) {
            $key = 'author_' . md5($authorString);

            $author = Cache::tags('authors')->remember($key, 300, function () use ($authorString) {
                return Author::where('name', $authorString)->first();
            });

            if (!$author) {
                DB::transaction(function () use (&$author, $authorString, $key) {
                    $author = Author::create([
                        'name' => $authorString
                    ]);

                    Cache::forget($key);
                });
            }

            $authors->push($author);
        }


        return $authors;
    }

    private function _fetchOrCreateCategories(array $categoriesArray)
    {
        $categories = collect();
        foreach ($categoriesArray as $key => $categoryString) {
            $key = 'category_' . md5($categoryString);

            $category = Cache::tags('categories')->remember($key, 300, function () use ($categoryString) {
                return Category::where('name', $categoryString)->first();
            });

            if (!$category) {
                DB::transaction(function () use (&$category, $categoryString, $key) {
                    $category = Category::create([
                        'name' => $categoryString
                    ]);

                    Cache::forget($key);
                });
            }

            $categories->push($category);
        }


        return $categories;
    }

    private function _buildMetaData(array $json, Collection &$metaData)
    {
        $bookJson = $json['items'][0];

        $metaData->put('title', $bookJson['volumeInfo']['title']);

        $metaData->put('sub_title', isset($bookJson['volumeInfo']['subtitle']) ? $bookJson['volumeInfo']['subtitle'] : null);

        $metaData->put('authors', $bookJson['volumeInfo']['authors']);

        $metaData->put('publisher', isset($bookJson['volumeInfo']['publisher']) ? $bookJson['volumeInfo']['publisher'] : null);

        $metaData->put(
            'publish_date',
            isset($bookJson['volumeInfo']['publisher']) ? Carbon::parse($bookJson['volumeInfo']['publishedDate'])->toDateString() : null
        );

        $metaData->put('description', $bookJson['volumeInfo']['description']);

        $metaData->put('edition', isset($bookJson['volumeInfo']['edition']) ? $bookJson['volumeInfo']['edition'] : null);

        $bookJson['volumeInfo']['industryIdentifiers'][] = [
            'type' => 'GOOGLE',
            'identifier' => $bookJson['id'],
        ];

        $metaData->put('identifiers', $bookJson['volumeInfo']['industryIdentifiers']);

        $metaData->put('page_count', isset($bookJson['volumeInfo']['pageCount']) ? $bookJson['volumeInfo']['pageCount'] : null);

        $metaData->put('categories', isset($bookJson['volumeInfo']['categories']) ? $bookJson['volumeInfo']['categories'] : []);

        $metaData->put('language', $bookJson['volumeInfo']['language']);


        $response = Http::get('https://www.googleapis.com/books/v1/volumes/' . $bookJson['id']);
        if ($response->successful()) {
            $volume = $response->json();

            if ($metaData['publisher'] === null) {
                $metaData->put('publisher', isset($volume['volumeInfo']['publisher']) ? $volume['volumeInfo']['publisher'] : null);
            }
            if ($metaData['sub_title'] === null) {
                $metaData->put('sub_title', isset($volume['volumeInfo']['subtitle']) ? $volume['volumeInfo']['subtitle'] : null);
            }
            if ($metaData['categories'] === null) {
                $metaData->put('categories', isset($volume['volumeInfo']['categories']) ? $volume['volumeInfo']['categories'] : []);
            }
            if ($metaData['publish_date'] === null) {
                $metaData->put(
                    'publish_date',
                    isset($bookJson['volumeInfo']['publishedDate']) ? Carbon::parse($bookJson['volumeInfo']['publishedDate'])->toDateString() : null
                );
            }

            if (isset($volume['volumeInfo']['imageLinks']) && !isset($volume['volumeInfo']['imageLinks']['small'])) {
                $volume['volumeInfo']['imageLinks']['small'] = 'https://books.google.com/books/content/images/frontcover/' . $volume['id'] . '?fife=w400-h600';
            }

            $metaData->put('categories', isset($volume['volumeInfo']['categories']) ? $volume['volumeInfo']['categories'] : []);
            $metaData->put('images', isset($volume['volumeInfo']['imageLinks']) ? $volume['volumeInfo']['imageLinks'] : []);
        }
    }

    private function _fetchGoogleMetadata(array $toProcess, array $isbns, Collection &$metaData)
    {
        if (count($isbns['ebook']) > 0) {
            foreach ($isbns['ebook'] as $key => $isbn) {
                $response = Http::get('https://www.googleapis.com/books/v1/volumes?q=isbn:' . $isbn);

                if ($response->successful()) {
                    $json = $response->json();
                    if ($json['totalItems'] > 0) {
                        $this->_buildMetaData($json, $metaData);
                    } else {
                        $this->error($response->body());
                    }

                    // $xml = simplexml_load_string($xml_string);

                    // $json = json_encode($xml);

                    // $array = json_decode($json, TRUE);

                    break;
                } else {
                    $this->error($response->body());
                }

                // $this->info($response->body());
            }
        } elseif (count($isbns['book']) > 0) {
            foreach ($isbns['book'] as $key => $isbn) {
                // $this->error('FETCHING ISBN: ' . $isbn);
                $response = Http::get('https://www.googleapis.com/books/v1/volumes?q=isbn:' . $isbn);

                if ($response->successful()) {
                    $json = $response->json();
                    if ($json['totalItems'] > 0) {
                        $this->_buildMetaData($json, $metaData);
                    } else {
                        $this->error($response->body());
                    }

                    // $xml = simplexml_load_string($xml_string);

                    // $json = json_encode($xml);

                    // $array = json_decode($json, TRUE);

                    break;
                } else {
                    $this->error($response->body());
                }

                // $this->info($response->body());
            }
        }

        //
    }

    private function _findISBNs(array &$isbns, string $fileContents, $printLines = false)
    {
        foreach (preg_split("/\\r\\n|\\r|\\n/", $fileContents) as $key => $line) {

            // if ($printLines) {
            //     $this->line(preg_replace('/(?<=\d)\s+(?=\d)/', '', str_replace(['-'], '', $line)));
            //     $this->newLine();
            // }

            preg_match_all(
                '/(ebook.*?isbn[:]?.*?[0-9]{13}|ebook.*?isbn[:]?.*?[0-9]{9}[a-zA-z]{1})|(isbn[:]?.*?[0-9]{13}|isbn[:]?.*?[0-9]{9}[a-zA-z]{1}|\s+?[0-9]{13}|\s+?[0-9]{9}[a-zA-z]{1})/i',
                preg_replace('/(?<=\d)\s+(?=\d)/', '', str_replace(['-'], '', $line)),
                $matches,
                PREG_PATTERN_ORDER
            );
            if (count($matches[0]) > 0) {
                // $this->info(preg_replace('/(?<=\d)\s+(?=\d)/', '', str_replace(['-'], '', $line)));

                foreach ($matches[0] as $key => $match) {
                    if ($printLines) {
                        $this->line($match);
                        $this->newLine();
                    }

                    $isbn = substr($match, -13);
                    if (!preg_match('/^[0-9]{13}$/', $isbn)) {
                        $isbn = substr($match, -10);

                        if (!preg_match('/^[0-9]{9}[a-zA-z]{1}$/', $isbn)) {
                            // Could not find an ISBN...
                            continue;
                        }
                    }

                    if (str_contains(strtolower($match), 'ebook')) {
                        if (!in_array($isbn, $isbns['ebook']))
                            $isbns['ebook'][] = $isbn;
                    } else {
                        if (!in_array($isbn, $isbns['book']))
                            $isbns['book'][] = $isbn;
                    }

                    // $isbnArray = explode(' ', $match);

                    // // Get the ISBN and strip any weird characters
                    // $isbn = trim(end($isbnArray), "\xc2\xa0");

                    // if (strlen($isbn) !== 10 && strlen($isbn) !== 13) {
                    //     $this->error('error');
                    //     $isbnArray = explode("\t", $match);
                    // }

                    // // Get the ISBN and strip any weird characters
                    // $isbn = trim(end($isbnArray), "\xc2\xa0");

                    // if ($printLines) {
                    //     dump($isbnArray);
                    //     $this->line($isbn);
                    // }

                    // // only accept ISBN's that are 10 or 13 digits
                    // if (strlen($isbn) === 10 || strlen($isbn) === 13) {

                    //     // separate the ebook and regular ISBN's so we can prioritize ebooks

                    // }
                }
            }
        }
    }
}
