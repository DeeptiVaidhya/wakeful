import { Injectable } from '@angular/core';
import { Headers, Http } from '@angular/http';
import { Storage } from '@ionic/storage';
import 'rxjs/add/operator/map';
import { CONSTANTS } from '../config/constants';
@Injectable()
export class HomeworkServiceProvider {
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
	homeworks() {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				if(this.storage.get('course_id')){
					this.storage.get('course_id').then(course_id => {
						this.http.get(CONSTANTS.API_ENDPOINT + 'homework/category?course_id='+course_id, { headers: headers }).subscribe(
							res => this.successCallback(res, resolve, reject),
							err => this.errorCallback(err, resolve, reject)
						);
					});
				}else{
					this.http.get(CONSTANTS.API_ENDPOINT + 'homework/category?course_id='+CONSTANTS.CURRENT_COURSE, { headers: headers }).subscribe(
						res => this.successCallback(res, resolve, reject),
						err => this.errorCallback(err, resolve, reject)
					);
				}
				
			});
		});
	}

	/** Calling api for get class list */
	classList(data) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.post(CONSTANTS.API_ENDPOINT + 'homework/category_homework', data, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}

	/** Calling api for get homework detail */
	homework_detail(data) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.post(CONSTANTS.API_ENDPOINT + 'homework/homework-detail', data, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}

	/** Calling api for add status of exercise audio/video track */
	exerciseTracking(data) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.post(CONSTANTS.API_ENDPOINT + 'homework/exercise-tracking', data, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}

	updateReadingTime(data) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.post(CONSTANTS.API_ENDPOINT + 'homework/update-reading-time', data, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}
}