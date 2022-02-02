import { Component } from '@angular/core';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { LoadingController, NavController } from 'ionic-angular';
import { AuthServiceProvider } from '../../providers/auth-service';
import { Helper } from '../../providers/helper';

// import { AuthService } from "angular4-social-login";
// import { FacebookLoginProvider, GoogleLoginProvider } from "angular4-social-login";
// import { SocialUser } from "angular4-social-login";

@Component({
	selector: 'page-forgot-password',
	templateUrl: 'forgot-password.html',
	providers: [AuthServiceProvider],
})
export class ForgotPasswordPage {
	private forgotPasswordForm: FormGroup;
	loading: any;
	data: any;
	is_unique_email: Boolean = false;
	is_unique_email_msg = '';
	is_success = false;

	constructor(
		public loadCtrl: LoadingController,
		public navCtrl: NavController,
		private formBuilder: FormBuilder,
		private authService: AuthServiceProvider,
		public helper: Helper,
	) {

		this.forgotPasswordForm = this.formBuilder.group({
			email: [
				'',
				Validators.compose([
					Validators.pattern(
						/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
					),
					Validators.required,
				]),
			],
		});
	}

	forgotPassword() {
		if (this.forgotPasswordForm.valid && this.is_unique_email) {
			this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
			this.loading.present();
			this.authService.forgot_password(this.forgotPasswordForm.value).then(
				result => {
					this.data = result;
					this.loading.dismiss();
					if (this.data.status != 'error') {
						this.is_success = true;
					} else {
						if(this.data.data){
							this.is_unique_email = false;
							this.is_unique_email_msg = 'Email address does not exist in system.';
						}
					}
				},
				err => {
					this.loading.dismiss();
					this.helper.presentToast('Form Invalid', 'error');
				}
			);
		} else {
			this.validateAllFormFields(this.forgotPasswordForm);
		}
	}

	// input is focused or not
	isTouched(ctrl, flag) {
		this.forgotPasswordForm.controls[ctrl]['hasFocus'] = flag;
	}

	// Check Email is exits or not
	isEmailUnique() {
		if (this.forgotPasswordForm.controls['email'].valid) {
			this.authService.isEmailRegisterd({ 'current_email': this.forgotPasswordForm.controls['email'].value }).then(
				result => {
					this.data = result;
					this.is_unique_email = this.data.status == 'success';
					this.is_unique_email_msg = this.is_unique_email ? '' : 'Email address does not exist in system.';
					this.is_unique_email && this.forgotPassword();
				},
				err => {
				}
			);
		} else {
			this.is_unique_email = true;
			this.is_unique_email_msg='';
			this.validateAllFormFields(this.forgotPasswordForm);
		}
	}

	validateAllFormFields(formGroup: FormGroup) {
		//{1}
		Object.keys(formGroup.controls).forEach(field => {
			//{2}
			const control = formGroup.get(field); //{3}
			if (control instanceof FormControl) {
				//{4}
				control.markAsTouched({ onlySelf: true });
			} else if (control instanceof FormGroup) {
				//{5}
				this.validateAllFormFields(control); //{6}
			}
		});
	}

}

