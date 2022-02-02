import { Component, ElementRef, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Storage } from '@ionic/storage';
import { IonicPage, LoadingController, NavController } from 'ionic-angular';
import { MenuController } from 'ionic-angular/components/app/menu-controller';
import { AuthServiceProvider } from '../../providers/auth-service';
import { Helper } from '../../providers/helper';
/**
 * Generated class for the ProfilePage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
	selector: 'page-profile',
	templateUrl: 'profile.html',
})
export class ProfilePage {
	@ViewChild('fileInput') fileInput: ElementRef;
    @ViewChild('imageContainer') imageContainer: ElementRef;
    profileMenPics = [
    	{title:'Man 1',src:'men-1.png'},
    	{title:'Man 2',src:'men-2.png'},
    	{title:'Man 3',src:'men-3.png'},
    	{title:'Man 4',src:'men-4.png'},
    	{title:'Man 5',src:'men-5.png'},
    	{title:'Man 6',src:'men-6.png'},
    	{title:'Man 7',src:'men-7.png'},
    	{title:'Man 8',src:'men-8.png'},
    	{title:'Man 9',src:'men-9.png'},
    	{title:'Man 10',src:'men-10.png'},
    	{title:'Man 11',src:'men-11.png'},
    	{title:'Man 12',src:'men-12.png'},
    	{title:'Man 13',src:'men-13.png'},
    	{title:'Man 14',src:'men-14.png'},
    	{title:'Man 15',src:'men-15.png'},
    	{title:'Man 16',src:'men-16.png'},
    	{title:'Man 17',src:'men-17.png'},
    	{title:'Man 18',src:'men-18.png'},
    	{title:'Man 19',src:'men-19.png'},
		{title:'Man 20',src:'men-20.png'},
	];
	profileWomenPics =[
		{title:'Woman 1',src:'women-1.png'},
    	{title:'Woman 2',src:'women-2.png'},
    	{title:'Woman 3',src:'women-3.png'},
    	{title:'Woman 4',src:'women-4.png'},
    	{title:'Woman 5',src:'women-5.png'},
    	{title:'Woman 6',src:'women-6.png'},
    	{title:'Woman 7',src:'women-7.png'},
    	{title:'Woman 8',src:'women-8.png'},
    	{title:'Woman 9',src:'women-9.png'},
    	{title:'Woman 10',src:'women-10.png'},
    	{title:'Woman 11',src:'women-11.png'},
    	{title:'Woman 12',src:'women-12.png'},
    	{title:'Woman 13',src:'women-13.png'},
    	{title:'Woman 14',src:'women-14.png'},
    	{title:'Woman 15',src:'women-15.png'},
    	{title:'Woman 16',src:'women-16.png'},
    	{title:'Woman 17',src:'women-17.png'},
    	{title:'Woman 18',src:'women-18.png'},
    	{title:'Woman 19',src:'women-19.png'},
    	{title:'Woman 20',src:'women-20.png'},
	];
	private profileForm: FormGroup;
	data: any;
	profile_picture:any;
	error_message: any[];
	is_unique_email: Boolean = true;
	is_unique_username: Boolean = true;
	is_current_password: Boolean = true;
	is_unique_email_msg = '';
	is_unique_username_msg = '';
	is_current_password_msg = '';
	is_previous_password: Boolean = true;
	overlayHidden: boolean = false;
	is_previous_password_msg = '';
	allowed_symbol = "$@!%*?&";
	isNotAllowedSymbol = false;
	loading: any;
	user_detail: Object = {
		// first_name: '',
		// last_name: '',
		unique_id:'',
		email: '',
		username: '',
		profile_picture: '',
	};

	constructor(
		public loadCtrl: LoadingController,
		public navCtrl: NavController,
		public menu: MenuController,
		private formBuilder: FormBuilder,
		private authService: AuthServiceProvider,
		public helper: Helper,
		public storage: Storage,
	) {
		this.menu.enable(true);
		this.getUserProfile();
		this.profileForm = this.formBuilder.group(
			{
				// unique_id: [
				// 	this.user_detail['unique_id']
				// ],
				// first_name: [
				// 	this.user_detail['first_name'],
				// 	Validators.compose([Validators.pattern(/^\s*[a-z]+\s*$/i), Validators.required]),
				// ],
				// last_name: [
				// 	this.user_detail['last_name'],
				// 	Validators.compose([Validators.pattern(/^\s*[a-z]+\s*$/i), Validators.required]),
				// ],
				email: [
					this.user_detail['email'],
					Validators.compose([
						Validators.pattern(
							/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
						),
						Validators.required,
					]),
				],
				username: [
					this.user_detail['username'],
					Validators.compose([Validators.pattern(/^\s*[a-z0-9\@\.\$\#\_]+\s*$/i), Validators.required]),
				],
				current_password: [''],
				password: [
					'',
					Validators.compose([
						Validators.pattern(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@!%*?&])[A-Za-z\d$@!%*?&]{8,}/),
						// Validators.pattern(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,}/),
					]),
				],
				confirm_password: [''],
				profile_picture: '',
				previous_image: this.user_detail['profile_picture'],
			},
			{
				validator: this.matchingPasswords('password', 'confirm_password'),
			}
		);
	}

	// input is focused or not
	isTouched(ctrl, flag) {
		this.profileForm.controls[ctrl]['hasFocus'] = flag;
	}

	ionViewCanEnter() {
		this.helper.authenticated().then(
			response => {
				return true;
			},
			err => {
				return false;
			}
		);
	}

	// Password match validation
	matchingPasswords(passwordKey: string, confirmPasswordKey: string) {
		return (group: FormGroup): { [key: string]: any } => {
			let password = group.controls[passwordKey];
			let confirm_password = group.controls[confirmPasswordKey];

			if (password.value !== confirm_password.value) {
				return {
					mismatchedPasswords: true,
				};
			}
		};
	}

	// Check Email is exits or not
	isCurrentPassword(password) {
		if (this.profileForm.controls['password'].valid && password != '') {
			this.authService.isCurrentPassword({password: encodeURIComponent(password)}).then(
				result => {
					this.data = result;
					if (this.data.status == 'success') {
						this.is_current_password_msg = 'Incorrect current password.';
						this.is_current_password = false;
					} else {
						this.is_current_password_msg = '';
						this.is_current_password = true;
					}
				},
				err => {
					console.log(err);
				}
			);
		} else {
			this.is_current_password = true;
		}
	}

	// Check Previous Password
	isPreviousPassword(password) {
		if (this.profileForm.controls['password'].valid && password !== '') {
			this.authService.isPreviousPassword({password: encodeURIComponent(password)}).then(
				result => {
					this.data = result;
					if (this.data.status === 'success') {
						this.is_previous_password_msg = 'Your password must be different from the previous 6 passwords.';
						this.is_previous_password = false;
					} else {
						this.is_previous_password_msg = '';
						this.is_previous_password = true;
					}
				},
				err => {
					console.log(err);
				}
			);
		} else {
			this.is_previous_password = true;
		}
	}

	// Check Email is exits or not
	isEmailUnique(email) {
		if (this.profileForm.controls['email'].valid) {
			let email_info = {
				previous_email: this.user_detail['email'],
				current_email: email,
			};
			this.helper.isEmailUnique(email_info).then(
				response => {
					this.is_unique_email_msg = 'This email address is already exits.';
					this.is_unique_email = false;
				},
				err => {
					this.is_unique_email_msg = '';
					this.is_unique_email = true;
				}
			);
		}
	}

	// Check Username is exits or not
	isUsernameUnique(username) {
		if (this.profileForm.controls['username'].valid) {
			let username_info = {
				previous_username: this.user_detail['username'],
				current_username: username,
			};
			this.helper.isUsernameUnique(username_info).then(
				response => {
					this.is_unique_username_msg = 'This username is already exits.';
					this.is_unique_username = false;
				},
				err => {
					this.is_unique_username_msg = '';
					this.is_unique_username = true;
				}
			);
		}
	}

	getUserProfile() {
		this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
		this.loading.present();
		this.authService.get_profile().then(
			response => {
				this.loading.dismiss();
				this.data = response;
				if (this.data.status == 'success') {
					this.user_detail = this.data.data;
					this.getColor();
				}
			},
			err => {
				console.log(err);
			}
		);
	}

	updateProfile() {
		this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
		this.loading.present();
		let form = JSON.parse(JSON.stringify(this.profileForm.value));
		if (this.profileForm.valid &&  !this.notAllowedSymbol(this.profileForm.value['password'])) {
			form['previous_username'] = this.user_detail['username'];
			form['previous_email'] = this.user_detail['email'];
			
			form['password'] = encodeURIComponent(this.profileForm.value['password']);
			form['confirm_password'] = encodeURIComponent(this.profileForm.value['confirm_password']);
			form['current_password'] = encodeURIComponent(this.profileForm.value['current_password']);
			this.authService.update_profile(form).then(
				result => {
					this.data = result;
					this.loading.dismiss();
					if (this.data.status == 'success') {
						this.helper.presentToast(this.data.msg, this.data.status);
						this.getUserProfile();
					} else {
						this.helper.presentToast(this.data.msg, this.data.status);
					}
				},
				err => {
					var error = err.json();
					if (error.status == 'error') {
						this.error_message = error.data;
					}
					this.helper.presentToast(error.msg, error.status);
				}
			);
		} else {
			this.loading.dismiss();
		}
	}

	getColor() {
		let color_arr = ['bg-primary', 'bg-pink', 'bg-blue', 'bg-yellow', 'bg-secondary', 'bg-danger'],
			len = color_arr.length,
			name = this.user_detail['username'].toUpperCase(),//this.user_detail['first_name'].toUpperCase(),
			code = name.charCodeAt(0),
			index = (code - 65) % len,
			color = color_arr[index];
		let allElems = document.querySelectorAll('[data-profile_initials]');
		for (let i = 0, len = allElems.length; i < len; i++) {
			allElems[i].setAttribute('class', color + ' circle-large icon-circle-outline');
		}
	}

	logout() {
		this.helper.logout().then(
			response => {},
			err => {}
		);
	}

	removeAvatar(){
		this.user_detail['profile_picture']= '';
		this.profile_picture= '';
		this.profileForm.get('profile_picture').setValue('');
		setTimeout(()=>{
			this.getColor();
		},200);
	}


	public hideOverlay() {
		this.overlayHidden = !this.overlayHidden;
	}

	selectImage(imageName, imageTitle){
		this.profileForm.get('profile_picture').setValue(imageName);
		this.profile_picture = imageName;
		this.overlayHidden = !this.overlayHidden;
	}
	
	notAllowedSymbol(password) {
		let flag = (/[\\" "#'()+,-./:;<=>[\]^_`{|}~]/g.test(password));
		if (flag) {
			let error = "Allowed special characters are " + this.allowed_symbol + " only.";
			this.helper.presentToast(error, 'error');
		}

		this.isNotAllowedSymbol = flag;
		return flag;
	}
}