import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { LoadingController, NavController, NavParams, ModalController } from 'ionic-angular';
import { AuthServiceProvider } from '../../providers/auth-service';
import { Helper } from '../../providers/helper';
import { SigninPage } from '../signin/signin';
import { ModalComponent } from '../../components/modal/modal';

@Component({
	selector: 'page-signup',
	templateUrl: 'signup.html',
	providers: [AuthServiceProvider],
})
export class SignupPage {
	signupForm: FormGroup;
	data: any;
	register_token: any;
	email: any;
	id: any;
	error_message: any[];
	is_unique_email: Boolean = false;
	is_unique_username: Boolean = false;
	is_unique_email_msg = '';
	is_unique_username_msg = '';
	submitAttempt = false;
	loading: any;
	signInPage = SigninPage;
	code:any;
	allowed_symbol = "$@!%*?&";
	isNotAllowedSymbol = false;
	description = {
		script : 'Caring for Yourself',
		description : '<p>The guided mindfulness training information and practices offered through Wakeful include mindfulness meditation and gentle mindful movement exercises. Please consult a qualified health care professional if you are new to mindful movement or suffer from any physical condition(s) that may limit your movement.  Should you experience any pain or discomfort, please listen to your body and discontinue the activity.</p><p> Mindfulness skills are not intended to serve as a substitute for consultation and/or treatment from a qualified heath care professional. As such, it is always the primary responsibility of the individual engaging in these practices to seek appropriate assessment, advice, support, and treatment from a qualified health professional if they have concerns about their mental or physical health. If you are experiencing problems such as anxiety, depression, insomnia, or some other type of illness while using the Wakeful tool, please consult a qualified health care professional.</p>'
	} 
	// Constructor
	constructor(
		public loadCtrl: LoadingController,
		public navCtrl: NavController,
		private formBuilder: FormBuilder,
		private authService: AuthServiceProvider,
		public modalCtrl: ModalController,
		public helper: Helper,
		public navParams: NavParams,
	) {
		this.code = this.navParams.get('accesscode');
		// form validations
		this.signupForm = this.formBuilder.group(
			{
			token: [''],
			email: [
					'',
				],
				username: ['', Validators.compose([Validators.pattern(/^\s*[a-z0-9\@\.\$\#\_]+\s*$/i), Validators.required])],
				id: ['', Validators.required],
				password: [
					'',
					Validators.compose([
						Validators.pattern(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@!%*?&])[A-Za-z\d$@!%*?&]{8,}/),
						Validators.required,
					]),
				],
				confirm_password: ['', Validators.required],
				term_condition: [1, Validators.required],
			},
			{
				validator: this.matchingPasswords('password', 'confirm_password'),
			}
		);
	}
	
	ionViewWillEnter(){
		this.checkCode();
	}
   // check code is authorized
	checkCode() {
		if (this.code != undefined) {
			let data = { code: this.code };
		
			this.authService.signup_user_data(data).then(
				result => {
					if(result['status'] == 'success'){
					 	if(result['result']['is_authorized'] == 1){
					 		this.navCtrl.setRoot(SigninPage);
					 	} else {
					 		this.email = result['result']['email'];
							this.register_token = result['result']['register_token'];
							this.id = result['result']['id'];
					 	}
					} else {
						this.helper.presentToast('This link is no longer valid', 'error');
					 	this.navCtrl.setRoot(SigninPage);
					}
				}
			);
		}
	}



	// Login page navigation
	public login() {
		this.navCtrl.push(SigninPage);
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

	// input is focused or not
	isTouched(ctrl,flag) {

		//this.signupForm.controls[ctrl]['value']=this.signupForm.controls[ctrl]['value'].trim();
		this.signupForm.controls[ctrl]['hasFocus']=flag;
	}

	// Check Email is exits or not
	isEmailUnique(email) {
		if (this.signupForm.controls['email'].valid) {
			let email_info = { 'current_email': email.trim() };
			this.helper.isEmailUnique(email_info).then(
				response => {
					this.is_unique_email_msg = 'Email address already exits.';
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
		if (this.signupForm.controls['username'].valid) {
			let username_info = { 'current_username': username.trim() };
			this.helper.isUsernameUnique(username_info).then(
				response => {
					this.is_unique_username_msg = 'Username already exits.';
					this.is_unique_username = false;
				},
				err => {
					this.is_unique_username_msg = '';
					this.is_unique_username = true;
				}
			);
		}
	}

	// Save User Data
	signup() {
		this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
		this.loading.present();
		if (this.signupForm.valid && !this.notAllowedSymbol(this.signupForm.value['password'])) {
			let form = JSON.parse(JSON.stringify(this.signupForm.value));
			form['password'] = encodeURIComponent(this.signupForm.value['password']);
			form['confirm_password'] = encodeURIComponent(this.signupForm.value['confirm_password']);
			
			this.authService.signup(form).then(
				result => {
					this.data = result;
					this.loading.dismiss();
					if (this.data.status == 'success') {
						localStorage.setItem('token', this.data.token);
						this.helper.presentToast(this.data.msg, this.data.status);
						this.navCtrl.setRoot(SigninPage);
					} else {
						this.helper.presentToast(this.data.msg, this.data.status);
					}
				},
				err => {
					var error = err.json();
					this.loading.dismiss();
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
	notAllowedSymbol(password) {
		let flag = (/[\\" "#'()+,-./:;<=>[\]^_`{|}~]/g.test(password));
		if (flag) {
			let error = "Allowed special characters are " + this.allowed_symbol + " only.";
			this.helper.presentToast(error, 'error');
		}

		this.isNotAllowedSymbol = flag;
		return flag;
	}

	// Show script content in modal
	showAlert(script, description) {
		let alert = this.modalCtrl.create(ModalComponent, { 'body': description, 'title': script, 'bgColor': "#604a70", });
		alert.present();
	}
}
