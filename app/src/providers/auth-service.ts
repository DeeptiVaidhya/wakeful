import { Injectable } from '@angular/core';
import { Headers, Http } from '@angular/http';
import { Storage } from '@ionic/storage';
import { Events } from 'ionic-angular';
import 'rxjs/add/operator/map';
import { CONSTANTS } from '../config/constants';
import 'rxjs/add/operator/toPromise';
@Injectable()
export class AuthServiceProvider {
	constructor(private http: Http, public storage: Storage, private events:Events) { }

	/**
	 * @desc Used to add headers for each API call
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


	/** Calling api for check user is login or not */
	check_login() {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.storage.get('study_id').then(study_id => {
					this.http.post(CONSTANTS.API_ENDPOINT + 'auth/check_login', {study_id:study_id}, { headers: headers }).subscribe(
						res => {
							const contentType = res.headers.get('Content-type');
							if (contentType.indexOf('application/json') != -1) {
								let response = res.json();
								if (response.status == 'INVALID_TOKEN') {
									this.storage.clear();
									resolve(false);
								} else {
									response.hasOwnProperty('settings') &&
									this.storage.set('course_settings', response.settings);
									this.events.publish('course:updateSettings',response.settings);
									resolve(true);
								}
							} else {
								this.storage.clear();
								resolve(false);
							}
						},
						err => {
							this.storage.clear();
							resolve(false);
						}
					);
				});
			});
		});
	}

	/** ACalling api for for user login with username/email and password */

	login(credentials) {
		return new Promise((resolve, reject) => {
			this.http.post(CONSTANTS.API_ENDPOINT + 'auth/login', credentials, { headers: this.getHeaders() })
				//.map(res => res)
				.subscribe(
				res => this.successCallback(res, resolve, reject),
				err => this.errorCallback(err, resolve, reject)
				);
		});
	}

	/** Calling api for for user signup with user detail */

	signup_user_data(data){
		return new Promise((resolve, reject) => {
			this.http.post(CONSTANTS.API_ENDPOINT + 'auth/user-data', data, { headers: this.getHeaders() }).subscribe(
				res => this.successCallback(res, resolve, reject),
				err => this.errorCallback(err, resolve, reject)
			);
		});
	}

	signup(data) {
		return new Promise((resolve, reject) => {
			this.http.post(CONSTANTS.API_ENDPOINT + 'auth/signup', data, { headers: this.getHeaders() }).subscribe(
				res => this.successCallback(res, resolve, reject),
				err => this.errorCallback(err, resolve, reject)
			);
		});
	}

	get_course_id(){
		return new Promise((resolve) => {
			this.storage.get('course_id').then(course_id=>{
				resolve(course_id);
				return true;
			});
		});
	}

	check_user_class_count(data){
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.get(CONSTANTS.API_ENDPOINT + 'classes/check-user-class-read-count/'+data.course_id, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		})
	}
	/** Calling api for user login/signup with the help of facebook/gmail */

	social_login(data) {
		return new Promise((resolve, reject) => {
			this.http.post(CONSTANTS.API_ENDPOINT + 'auth/social-login', data, { headers: this.getHeaders() }).subscribe(
				res => this.successCallback(res, resolve, reject),
				err => this.errorCallback(err, resolve, reject)
			);
		});
	}

	/** Calling api for check email is exist in system or not */

	isEmailRegisterd(data) {
		return new Promise((resolve, reject) => {
			this.http.post(CONSTANTS.API_ENDPOINT + 'auth/check-email', data, { headers: this.getHeaders() }).subscribe(
				res => this.successCallback(res, resolve, reject),
				err => this.errorCallback(err, resolve, reject)
			);
		});
	}

	/** Calling api for check username is exist in system or not */

	isUsernameRegisterd(data) {
		return new Promise((resolve, reject) => {
			this.http.post(CONSTANTS.API_ENDPOINT + 'auth/check-username', data, { headers: this.getHeaders() }).subscribe(
				res => this.successCallback(res, resolve, reject),
				err => this.errorCallback(err, resolve, reject)
			);
		});
	}

	/** Calling api for logout function and expire user token  */

	logout() {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.get(CONSTANTS.API_ENDPOINT + 'auth/logout', { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}

	/** Calling api for get login user detail */

	get_profile() {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.get(CONSTANTS.API_ENDPOINT + 'auth/profile', { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}

	/** Calling api for check  login user current password */

	isCurrentPassword(data) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.post(CONSTANTS.API_ENDPOINT + 'auth/check_password', data, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}


	/**
	 * @desc Cheching a password entered is previous or not, called from edit profile page
	 * @param data 
	 */

	isPreviousPassword(data) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.post(CONSTANTS.API_ENDPOINT + 'auth/check-previous-password', data, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}


	/** Calling api for update login user detail */

	update_profile(data) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.post(CONSTANTS.API_ENDPOINT + 'auth/profile', data, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}

	/** Calling api for forgot password */
	forgot_password(data) {
		return new Promise((resolve, reject) => {
			this.http.post(CONSTANTS.API_ENDPOINT + 'auth/forgot_password', data, { headers: this.getHeaders() }).subscribe(
				res => this.successCallback(res, resolve, reject),
				err => this.errorCallback(err, resolve, reject)
			);
		});
	}

	/** Calling api for forgot password */
	reset_password_code(data) {
		return new Promise((resolve, reject) => {
			this.http.post(CONSTANTS.API_ENDPOINT + 'auth/reset_password_code', data, { headers: this.getHeaders() }).subscribe(
				res => this.successCallback(res, resolve, reject),
				err => this.errorCallback(err, resolve, reject)
			);
		});
	}

	/** Calling api for forgot password */
	reset_password(data) {
		return new Promise((resolve, reject) => {
			this.http.post(CONSTANTS.API_ENDPOINT + 'auth/reset_password', data, { headers: this.getHeaders() }).subscribe(
				res => this.successCallback(res, resolve, reject),
				err => this.errorCallback(err, resolve, reject)
			);
		});
	}


	/**
	 * @desc Calling api for Contact Us 
	 * @param data 
	*/
	contact_us(data) {
		return new Promise((resolve, reject) => {
			this.http.post(CONSTANTS.API_ENDPOINT + 'auth/contact_us', data, { headers: this.getHeaders() }).subscribe(
				res => this.successCallback(res, resolve, reject),
				err => this.errorCallback(err, resolve, reject)
			);
		});
	}

	getNotificationCount(){
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.get(CONSTANTS.API_ENDPOINT + 'auth/check-notification-count', { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}

	clearNotificationCount(){
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.get(CONSTANTS.API_ENDPOINT + 'auth/clear-notification-count', { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}
	
	accessedResources(data) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.post(CONSTANTS.API_ENDPOINT + 'auth/accessed-resources', data, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}

}
