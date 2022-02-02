import { Component } from '@angular/core';
import { NavController, IonicPage, LoadingController } from 'ionic-angular';
import { Validators, FormBuilder, FormGroup } from '@angular/forms';
import { Helper } from '../../providers/helper';
import { AuthServiceProvider } from '../../providers/auth-service';

@IonicPage()
@Component({
	selector: 'page-contact',
	templateUrl: 'contact.html',
})
export class ContactPage {
	private form: FormGroup;
	loading: any;
	constructor(
		public loadCtrl: LoadingController,
		public navCtrl: NavController,
		private formBuilder: FormBuilder,
		private authService: AuthServiceProvider,
		public helper: Helper) {
		this.form = this.formBuilder.group(
			{
				first_name: [
					'',
					Validators.compose([Validators.required, Validators.pattern(/^\s*[a-z]+\s*$/i), Validators.required]),
				],
				last_name: [
					'',
					Validators.compose([Validators.required, Validators.pattern(/^\s*[a-z]+\s*$/i), Validators.required]),
				],
				email: [
					'',
					Validators.compose([
						Validators.pattern(
							/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
						),
						Validators.required,
					]),
				],
				message: [
					'',
					Validators.compose([Validators.required]),
				],
			}
		);

	}

	// input is focused or not
	isTouched(ctrl, flag) {
		this.form.controls[ctrl]['hasFocus'] = flag;
	}

	contactUs() {
		if (this.form.valid) {
			this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
			this.loading.present();
			this.authService.contact_us(this.form.value).then(
				result => {
					const response = result;
					this.loading.dismiss();
					if (response['status'] == 'success') {
						this.helper.presentToast(response['msg'], response['status']);
						this.navCtrl.setRoot(this.navCtrl.getActive().component);
					} else {
						this.helper.presentToast(response['msg'], response['status']);
					}
				},
				err => {
				}
			);
		} 
	}
}
