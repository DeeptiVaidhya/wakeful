import { Injectable } from '@angular/core';
import { Headers, Http } from '@angular/http';
import { Storage } from '@ionic/storage';
import { Platform } from 'ionic-angular';
import 'rxjs/add/operator/map';
import { CONSTANTS } from '../config/constants';

@Injectable()
export class ClassServiceProvider {
	constructor(private http: Http, private storage: Storage, private platform: Platform) { }

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
	get_image(bg) {
		let image;
		if (this.platform.is('tablet')) {
			image = bg.tablet;
		} else if (this.platform.is('mobile')) {
			image = bg.mobile;
		} else {
			image = bg.desktop;
		}
		return image;
	}

	get_background_images() {
		return new Promise(resolve => {
			this.storage.get('background_images').then(bg => {
				if (!bg) {
					this.set_background_images().then(res => {
						resolve(this.get_image(res));
					});
				} else {
					resolve(this.get_image(bg));
				}
			});
		});
	}

	set_background_images() {
		return new Promise(resolve => {
			this.http.get(CONSTANTS.API_ENDPOINT + 'auth/background-images', { headers: this.getHeaders() }).subscribe(
				res => {
					const contentType = res.headers.get('Content-type');
					if (contentType.indexOf('application/json') != -1) {
						let response = res.json();
						if (response.hasOwnProperty('data')) {
							this.storage.set('background_images', response.data);
							resolve(response.data);
						}
					} else {
					}
				})
				;
		});
	}

	/** Calling api for get  classes */
	classes() {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				if(this.storage.get('course_id')){
					this.storage.get('course_id').then(course_id => {
						this.http.get(CONSTANTS.API_ENDPOINT + 'classes/classes?course_id='+course_id, { headers: headers }).subscribe(
							res => this.successCallback(res, resolve, reject),
							err => this.errorCallback(err, resolve, reject)
						);
					});
				}else{
					this.http.get(CONSTANTS.API_ENDPOINT + 'classes/classes?course_id='+CONSTANTS.CURRENT_COURSE, { headers: headers }).subscribe(
						res => this.successCallback(res, resolve, reject),
						err => this.errorCallback(err, resolve, reject)
					);
				}
			});
		});
	}
	/**
	 * Get course details by id.
	 */
	course() {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				if(this.storage.get('course_id')){
					this.storage.get('course_id').then(course_id => {
						this.http.get(CONSTANTS.API_ENDPOINT + 'classes/course?course_id='+course_id, { headers: headers }).subscribe(
							res => this.successCallback(res, resolve, reject),
							err => this.errorCallback(err, resolve, reject)
						);
					});
				} else {
					this.http.get(CONSTANTS.API_ENDPOINT + 'classes/course?course_id='+CONSTANTS.CURRENT_COURSE, { headers: headers }).subscribe(
						res => this.successCallback(res, resolve, reject),
						err => this.errorCallback(err, resolve, reject)
					);
				}
			});
		});
	}

	/** Calling api for get setting */
	setting() {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				if(this.storage.get('study_id')){
					this.storage.get('study_id').then(study_id => {
						this.http.get(CONSTANTS.API_ENDPOINT + 'classes/setting?study_id='+study_id, { headers: headers }).subscribe(
							res => this.successCallback(res, resolve, reject),
							err => this.errorCallback(err, resolve, reject)
						);
					});
				}
			});
		});
	}

	/** Calling api for get dashboard detail */
	dashboard(course_id) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.get(CONSTANTS.API_ENDPOINT + 'classes/dashboard/'+course_id, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}

	/** Calling api for get class pages detail */
	getPage(data) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.post(CONSTANTS.API_ENDPOINT + 'classes/pages', data, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}

	/** Calling api for add reflection answer */
	addReflectionAnswer(data) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http
					.post(CONSTANTS.API_ENDPOINT + 'classes/reflection_answer', data, { headers: headers })
					.subscribe(
						res => this.successCallback(res, resolve, reject),
						err => this.errorCallback(err, resolve, reject)
					);
			});
		});
	}

	/** Calling api for add intention */
	addIntention(data) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http
					.post(CONSTANTS.API_ENDPOINT + 'classes/intention_answer', data, { headers: headers })
					.subscribe(
						res => this.successCallback(res, resolve, reject),
						err => this.errorCallback(err, resolve, reject)
					);
			});
		});
	}

	/** Calling api for add status of audio/video track */
	fileTracking(data) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.post(CONSTANTS.API_ENDPOINT + 'classes/file_tracking', data, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}

	/** Calling api get remaing time of udio/video track*/
	getTrackTime(file_info) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http
					.post(CONSTANTS.API_ENDPOINT + 'classes/get_file_tracking', file_info, { headers: headers })
					.subscribe(
						res => this.successCallback(res, resolve, reject),
						err => this.errorCallback(err, resolve, reject)
					);
			});
		});
	}

	/** Calling api for get current position of page */
	getCurrentPosition(classes_id) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http
					.get(CONSTANTS.API_ENDPOINT + 'classes/position?classes_id=' + classes_id, { headers: headers })
					.subscribe(
						res => this.successCallback(res, resolve, reject),
						err => this.errorCallback(err, resolve, reject)
					);
			});
		});
	}

	/** Calling api for get feedback information of course  */
	feedback() {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				if(this.storage.get('course_id')){
					//this.storage.get('course_id').then(course_id => {
					this.http.get(CONSTANTS.API_ENDPOINT + 'classes/feedback', { headers: headers }).subscribe(
						res => this.successCallback(res, resolve, reject),
						err => this.errorCallback(err, resolve, reject)
					);
					//});
				}else{
					this.http.get(CONSTANTS.API_ENDPOINT + 'classes/feedback', { headers: headers }).subscribe(
						res => this.successCallback(res, resolve, reject),
						err => this.errorCallback(err, resolve, reject)
					);
				}
			});
		});
	}

	/** Calling api for save  feedback answer */
	save_feedback(data) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.post(CONSTANTS.API_ENDPOINT + 'classes/feedback', data, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}

	/** Calling api for get current class detail */
	getCurrentClass(course_id) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.get(CONSTANTS.API_ENDPOINT + 'classes/current-class/'+course_id, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}

	/** Calling api for get current class detail */
	updateTime(data) {
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.post(CONSTANTS.API_ENDPOINT + 'classes/update-meditation-time', data, { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}

	classList(){
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				this.http.get(CONSTANTS.API_ENDPOINT + 'classes/classes-list', { headers: headers }).subscribe(
					res => this.successCallback(res, resolve, reject),
					err => this.errorCallback(err, resolve, reject)
				);
			});
		});
	}

	practiceFile(){
		return new Promise((resolve, reject) => {
			this.storage.get('token').then(token => {
				const headers = this.getHeaders();
				headers.append('Token', token);
				if(this.storage.get('course_id')){
					this.storage.get('course_id').then(course_id => {
						this.http.get(CONSTANTS.API_ENDPOINT + 'classes/practice-files/'+course_id, { headers: headers }).subscribe(
							res => this.successCallback(res, resolve, reject),
							err => this.errorCallback(err, resolve, reject)
						);
					});
				}else{
					this.http.get(CONSTANTS.API_ENDPOINT + 'classes/practice-files/'+CONSTANTS.CURRENT_COURSE, { headers: headers }).subscribe(
						res => this.successCallback(res, resolve, reject),
						err => this.errorCallback(err, resolve, reject)
					);
				}
			});
		});
	}
}
