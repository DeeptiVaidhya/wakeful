import { Component } from '@angular/core';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { App, LoadingController, NavController, NavParams } from 'ionic-angular';
import { AuthServiceProvider } from '../../providers/auth-service';
import { Helper } from '../../providers/helper';
import { HomePage } from '../home/home';

@Component({
	selector: 'page-reset-password',
	templateUrl: 'reset-password.html',
	providers: [AuthServiceProvider],
})
export class ResetPasswordPage {
	private resetPasswordForm: FormGroup;
	data: any;
	loading: any;
	code: String = '';
	Nav: any;
	is_success = false;
	is_password_valid: any = {
		'is_length': false,
		'is_space': false,
		'is_capital': false,
		'is_small': false,
		'is_symbol': false,
		'is_number': false
	}
	allowed_symbol = "$@!%*?&";
	// Add in constructor
	// private socialService: AuthService,
	constructor(
		public loadCtrl: LoadingController,
		public navCtrl: NavController,
		private formBuilder: FormBuilder,
		private authService: AuthServiceProvider,
		public navParams: NavParams,
		public helper: Helper,
		private app: App
	) {
		this.code = this.navParams.get('code');
		this.checkCode();
		this.resetPasswordForm = this.formBuilder.group(
			{
				password: [
					'',
					Validators.compose([
						// Validators.pattern(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,}/),
						Validators.required,
					]),
				],
				confirm_password: ['', Validators.required],
				code: [this.code],
			},
			{
				validator: this.matchingPasswords('password', 'confirm_password'),
			}
		);
	}

	public homePage() {
		this.Nav = this.app.getRootNavById('n4');
		this.Nav.setRoot(HomePage);
		this.refreshPage();
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

	// Save User Data
	resetPassword() {
		this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
		this.loading.present();

		if (this.resetPasswordForm.valid && this.check_password_validaty()) {
			const input = JSON.parse(JSON.stringify(this.resetPasswordForm.value));
			input.code = this.code;
			input.password = encodeURIComponent(this.resetPasswordForm.value['password']);
			input.confirm_password = encodeURIComponent(this.resetPasswordForm.value['confirm_password']);

			this.authService.reset_password(input).then(
				result => {
					this.data = result;
					this.loading.dismiss();
					if (this.data.status == 'success') {
						this.is_success = true;
						// this.Nav = this.app.getRootNavById('n4');
						// this.helper.presentToast(this.data.msg, this.data.status);
						// this.Nav.setRoot(HomePage);
						// this.refreshPage();
					} else {
						this.helper.presentToast(this.data.msg, this.data.status);
					}
				},
				err => {
					var error = err.json();
					this.loading.dismiss();
					this.helper.presentToast(error.msg, error.status);
				}
			);
		} else {
			this.loading.dismiss();
			this.validateAllFormFields(this.resetPasswordForm);
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

	checkCode() {
		if (this.code != undefined) {
			let data = { code: this.code };
			this.authService.reset_password_code(data).then(
				result => {
					this.data = result;
					if (this.data.status == 'error') {
						this.Nav = this.app.getRootNavById('n4');
						this.helper.presentToast(this.data.msg, this.data.status);
						this.Nav.setRoot(HomePage);
						this.refreshPage();
					}
				},
				err => {
					this.Nav = this.app.getRootNavById('n4');
					this.helper.presentToast('Forgot passowrd link is invalid', 'error');
					this.Nav.setRoot(HomePage);
					this.refreshPage();
				}
			);
		}
	}

	refreshPage() {
		var uri = window.location.toString();
		if (uri.indexOf('?') > 0) {
			var clean_uri = uri.substring(0, uri.indexOf('?'));
			window.history.replaceState({}, document.title, clean_uri);
		}
	}
	check_password_validaty() {
		let p = this.is_password_valid;

		if (p.is_not_symbol) {
			let error = 'Allowed special characters are ' + this.allowed_symbol + ' only.';
			this.helper.presentToast(error, 'error');
			this.is_password_valid.is_symbol = !1;
		}

		return (p.is_length && p.is_space && p.is_capital && p.is_small && p.is_symbol && p.is_number && !p.is_not_symbol);
	}
	checkPassword(password) {
		this.is_password_valid = {
			'is_length': !(password.length < 8),
			'is_space': !(/\s/g.test(password)),
			'is_capital': (/[A-Z]/g.test(password)),
			'is_small': (/[a-z]/g.test(password)),
			'is_symbol': (/[$@!%*?&]/g.test(password)),
			'is_not_symbol': (/[\\" "#'()+,-./:;<=>[\]^_`{|}~]/g.test(password)),
			'is_number': (/\d/g.test(password))
		}
		if (this.is_password_valid.is_not_symbol) {
			this.is_password_valid.is_symbol = !1;
		}
	}
}
