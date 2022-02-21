// import Echo from 'laravel-echo'
// window.Pusher = require('pusher-js')
// import Pusher from 'pusher-js'
import _ from 'lodash'
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */
import axios from 'axios'

// window._ = require('lodash')
window._ = _

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

// window.axios = require('axios')
window.axios = axios

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// window.Pusher = Pusher

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
//     forceTLS: true,
//     enabledTransports: ['ws', 'wss'],
// })

window.createServerSideDatasource = function createServerSideDatasource(server) {
	return {
		getRows: function (params) {
			console.log('[Datasource] - rows requested by grid: ', params.request)
			// get data for request from our fake server
			var response = server.getData(params.request)

			if (response.success) {
				// supply rows for requested block to grid
				params.success({ rowData: response.rows })
			} else {
				params.fail()
			}
		},
	}
}

window.createFakeServer = function createFakeServer(allData) {
	return {
		getData: function (request) {
			// take a copy of the data to return to the client
			var requestedRows = allData.slice()
			return {
				success: true,
				rows: requestedRows,
			}
		},
	}
}
