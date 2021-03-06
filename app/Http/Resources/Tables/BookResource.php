<?php

namespace App\Http\Resources\Tables;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BookResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'library' => $this->library?->name,
            'title' => $this->title,
            'sub_title' => $this->sub_title,
            'description' => $this->description,
            'authors' => $this->authors ? $this->authors->pluck('name')->implode(', ') : null,
            'size_on_disk' => $this->media ? $this->_sizeOnDisk($this->media) : null,
            'rating' => $this->rating,
            'categories' => $this->categories ? $this->categories->pluck('name')->implode(', ') : null,
            'series' => $this->series ? $this->series->pluck('name')->implode(', ') : null,
            'publisher' => $this->publisher?->name,
            'publish_date' => $this->publish_date,
            'language' => $this->language,
            'formats' => $this->media ? $this->_getFormats($this->media) : null,
            'thumbnail' => $this->images ? $this->_getThumbnail($this->images) : null,
            'details_image' => $this->images ? $this->_getDetailsImage($this->images) : null,
            'identifications' => IdentificationResource::collection($this->identifications)
        ];
    }

    private function _getDetailsImage(Collection $images)
    {
        foreach ($images as $key => $image) {
            if ($image->format === 'small') {
                return Storage::disk('books')->url($image->path);
            }
        }

        return null;
    }

    private function _getThumbnail(Collection $images)
    {
        foreach ($images as $key => $image) {
            if ($image->format === 'thumbnail') {
                return Storage::disk('books')->url($image->path);
            }
        }

        return null;
    }

    private function _getFormats(Collection $media)
    {
        $formats = [];
        foreach ($media as $key => $data) {
            // Log::info('format: ' . print_r($data, true));
            $formats[] = $data->file_format?->name ?? null;
        }

        $formats = array_unique($formats, SORT_STRING);

        return implode(', ', $formats);
    }

    private function _sizeOnDisk(Collection $media)
    {
        $bytes = 0;
        foreach ($media as $key => $data) {
            $bytes += (int) $data->size;
        }

        return $this->_humanFileSize($bytes);
    }

    private function _humanFileSize($size, $unit = "")
    {
        if (
            (!$unit && $size >= 1 << 30) || $unit == " GB"
        )
            return number_format($size / (1 << 30), 2) . " GB";

        if (
            (!$unit && $size >= 1 << 20) || $unit == " MB"
        )
            return number_format($size / (1 << 20), 2) . " MB";

        if (
            (!$unit && $size >= 1 << 10) || $unit == " KB"
        )
            return number_format($size / (1 << 10), 2) . " KB";

        return number_format($size) . " Bytes";
    }
}
