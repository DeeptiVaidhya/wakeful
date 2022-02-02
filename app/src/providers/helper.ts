import { Injectable } from '@angular/core';
import { Storage } from '@ionic/storage';
import { App, ToastController } from 'ionic-angular';
import { HomePage } from '../pages/home/home';
import { AuthServiceProvider } from '../providers/auth-service';
// import { NavController } from 'ionic-angular/navigation/nav-controller';

@Injectable()
export class Helper {
	private navCtrl: any;
	data: any;
	Nav: any;
	constructor(private toastCtrl: ToastController, public storage: Storage, private authService: AuthServiceProvider, private app: App) {
		this.navCtrl = app.getRootNavs();
	}

	// Helper function for show toast message 
	presentToast(msg, type) {
		let toast = this.toastCtrl.create({
			message: msg,
			duration: 5000,
			position: 'top',
			cssClass: type,
			showCloseButton: true,
			closeButtonText: 'X',
		});

		toast.onDidDismiss(() => {
		});

		toast.present();
	}

	// Middleware function for check user is authenticate or not 
	authenticated(redirect = true) {
		return new Promise((resolve, reject) => {
			this.authService.check_login().then(status => {
				if (status) {
					resolve(true);
				} else {
					if (redirect) {
						this.Nav = this.app.getRootNavById('n4');
						this.presentToast('Your session has been expired. Please log in to continue.', 'error');
						this.Nav.setRoot(HomePage);
					}

					reject(false);
				}
			});
		});
	}


	// Check User email exits or not 
	isEmailUnique(email_info) {
		return new Promise((resolve, reject) => {
			this.authService.isEmailRegisterd(email_info).then(result => {
				this.data = result;
				if (this.data.status == 'success') {
					resolve(true)
				} else {
					reject(false);
				}
			});
		});
	}

	// Check Username exits or not
	isUsernameUnique(user_info) {
		return new Promise((resolve, reject) => {
			this.authService.isUsernameRegisterd(user_info).then(result => {
				this.data = result;
				if (this.data.status == 'success') {
					resolve(true)
				} else {
					reject(false);
				}
			});
		});
	}

	// Comment logout function 
	logout() {
		return new Promise((resolve, reject) => {
			this.authService.logout().then(result => {
				this.data = result;
				if (this.data.status == 'success') {
					this.Nav = this.app.getRootNavById('n4');
					this.presentToast('You are logout successfully.', 'error');
					this.Nav.setRoot(HomePage);
					// this.app.getRootNav().setRoot(HomePage);
				} else {
					reject(false);
				}
			});
		});
	}

	get_course_setting(){
		return new Promise((resolve) => {
			this.storage.get('course_settings').then(course_settings=>{
				resolve(course_settings);
				return true;
			});
		});
	}





}
