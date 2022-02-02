import { Injectable } from '@angular/core';
import { Http, Headers } from '@angular/http';
import 'rxjs/add/operator/map';
import { CONSTANTS } from '../config/constants';
import { Storage } from '@ionic/storage';
@Injectable()
export class ReviewServiceProvider {
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

	/** Calling api for get review list of classes*/
	reviews() {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				if(this.storage.get('course_id')){
					this.storage.get('course_id').then(course_id => {
						this.http.get(CONSTANTS.API_ENDPOINT + 'review/reviews?course_id='+course_id, { headers: headers }).subscribe(
							res => this.successCallback(res, resolve, reject),
							err => this.errorCallback(err, resolve, reject)
						);
					});
				}else{
					this.http.get(CONSTANTS.API_ENDPOINT + 'review/reviews?course_id='+CONSTANTS.CURRENT_COURSE, { headers: headers }).subscribe(
						res => this.successCallback(res, resolve, reject),
						err => this.errorCallback(err, resolve, reject)
					);	
				}
			});
		});
	}

	/** Calling api for get review detail of review*/
	review_detail(review_id) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.get(CONSTANTS.API_ENDPOINT + 'review/review_detail?id=' + review_id, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}

	/** Calling api for add status of review audio/video track */
	reviewTracking(data) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.post(CONSTANTS.API_ENDPOINT + 'review/review_tracking', data, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}
}
