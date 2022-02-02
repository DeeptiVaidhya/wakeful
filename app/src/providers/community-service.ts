import { Injectable } from '@angular/core';
import { Http, Headers } from '@angular/http';
import 'rxjs/add/operator/map';
import { CONSTANTS } from '../config/constants';
import { Storage } from '@ionic/storage';
@Injectable()
export class CommunityServiceProvider {
	constructor(private http: Http, private storage: Storage) { }
	/**
	 * @desc Used to add headers for each API call, if a user is logged in then add token header also
	 * @param isLoggedIn 
	 */
	getHeaders() {
		return new Headers({
			'Content-Type': 'application/x-www-form-urlencoded',
			'Authorization': 'Basic ' + CONSTANTS.AUTH
		});
	}
	/**
	 * @desc Common Success Callback function used from all API calls
	 * @param res 
	 * @param resolve 
	 * @param reject 
	 * @param status 
	 */
	successCallback(res, resolve, reject, status = '') {
		if (res.headers.get('Content-type').indexOf('application/json') !== -1) {
			resolve(res.json());
		} else {
			reject({ status: 'error', msg: 'Invalid response' });
		}
	}

	/**
	 * @desc Common Error Callback function used from all API calls
	 * @param err 
	 * @param resolve 
	 * @param reject 
	 * @param status 
	 */
	errorCallback(err, resolve, reject, status = '') {
		if (err.headers.get('Content-type') === 'application/json') {
			reject(err.json().join());
		} else {
			console.log(err);
			reject({ status: 'error', msg: 'Invalid response' });
		}
	}
	/** Calling api for get homework of class */
	communities(page) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				if(this.storage.get('course_id')){
					this.storage.get('course_id').then(course_id => {
						this.http.get(CONSTANTS.API_ENDPOINT + 'community/communities?course_id='+course_id+'&page='+page, { headers: headers }).subscribe(
							res => this.successCallback(res, resolve, reject),
							err => this.errorCallback(err, resolve, reject)
						);
					});
				}else{
					this.http.get(CONSTANTS.API_ENDPOINT + 'community/communities?course_id='+CONSTANTS.CURRENT_COURSE+'&page='+page, { headers: headers }).subscribe(
						res => this.successCallback(res, resolve, reject),
						err => this.errorCallback(err, resolve, reject)
					);
				}
			});
		});
	}

	/** Calling api for get homework detail */
	discussion(data) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.post(CONSTANTS.API_ENDPOINT + 'community/discussion', data, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}

	/** Calling api for get single community of notification */
	community(data) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				if(this.storage.get('course_id')){
					this.storage.get('course_id').then(course_id => {
						this.http.get(CONSTANTS.API_ENDPOINT + 'community/notification_community?course_id='+course_id+'&question_id='+data.question_id+'&post_id='+data.post_id, { headers: headers }).subscribe(
							res => this.successCallback(res, resolve, reject),
							err => this.errorCallback(err, resolve, reject)
						);
					});
				}else{
					this.http.get(CONSTANTS.API_ENDPOINT + 'community/notification_community?course_id='+CONSTANTS.CURRENT_COURSE+'&question_id='+data.question_id+'&post_id='+data.post_id, { headers: headers }).subscribe(
						res => this.successCallback(res, resolve, reject),
						err => this.errorCallback(err, resolve, reject)
					);
				}
			});
		});
	}


	/** Calling api for add reflection answer */

	add_comment(data) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.post(CONSTANTS.API_ENDPOINT + 'community/reply', data, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}

	/** Calling api for get homework detail */

	get_reply(answer_id) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.get(CONSTANTS.API_ENDPOINT + 'community/reply?answer_id=' + answer_id, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}


	/** Calling api for get homework detail */

	add_comment_status(obj) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.post(CONSTANTS.API_ENDPOINT + 'community/comment_status', obj, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}


	/** Calling api for get homework detail */

	add_reply_status(obj) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.post(CONSTANTS.API_ENDPOINT + 'community/reply_status', obj, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}

	/** Calling api for get communities notification */
	notification(page) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.get(CONSTANTS.API_ENDPOINT + 'community/notification?page=' + page, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}

	/** Calling api for add reflection answer */

	updateNotification(data) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.post(CONSTANTS.API_ENDPOINT + 'community/update_notification', data, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}
}
