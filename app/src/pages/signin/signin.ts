import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Storage } from '@ionic/storage';
// import { AuthService, FacebookLoginProvider, GoogleLoginProvider } from 'angular4-social-login';
import { LoadingController, IonicPage,NavController, NavParams } from 'ionic-angular';
import { CONSTANTS } from '../../config/constants';
import { AuthServiceProvider } from '../../providers/auth-service';
import { Helper } from '../../providers/helper';
import { ClassPage } from '../class/class';
import { ForgotPasswordPage } from '../forgot-password/forgot-password';
import { WelcomeVideoPage } from '../welcome-video/welcome-video';
import { SignupPage } from '../signup/signup';

@IonicPage()
@Component({
	selector: 'page-login',
	templateUrl: 'signin.html',
	providers: [AuthServiceProvider],
})
export class SigninPage {
	private loginForm: FormGroup;
	loading: any;
	data: any;
	course: any;
	islogin: boolean = true;
	signupPage = SignupPage;
	forgotPasswordPage = ForgotPasswordPage;

	constructor(
		public loadCtrl: LoadingController,
		public navCtrl: NavController,
		private formBuilder: FormBuilder,
		private authService: AuthServiceProvider,
		public navParams: NavParams,
		public helper: Helper,
		private storage: Storage,
		// private socialService: AuthService
	) {
		this.course = this.navParams.get('course');
		this.loginForm = this.formBuilder.group({
			username: ['', Validators.required],
			password: ['', Validators.required],
			remember_me: ['false'],
			course: [(this.navParams.get('course')) ? ((this.navParams.get('course') !== 'course') ? this.navParams.get('course') : CONSTANTS.CURRENT_COURSE) : CONSTANTS.CURRENT_COURSE]
		});
	}

	signIn() {
		this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
		let form = JSON.parse(JSON.stringify(this.loginForm.value));
		this.loading.present();
		if (this.loginForm.value.remember_me) {
			this.setCookie("username", "password", 365);
		} else {
			this.setCookie("username", "password", -1);
		}
		form['password'] = encodeURIComponent(this.loginForm.value['password']);
		this.authService.login(form).then(
			result => {
				this.data = result;
				this.loading.dismiss();
				if (this.data.status != 'error') {
					this.storage.set('token', this.data.token);
					this.storage.set('course_id', this.data.course_id);
					this.storage.set('study_id', this.data.study_id);
					this.storage.set('username', this.data.username);
					this.storage.set('profile_picture', this.data.profile_picture);
					this.storage.set('play_video', true);
					this.islogin = false;
					var msg = (this.data.login_days < 7) ? this.data.msg : 'Welcome back! We know you left off at an earlier class, and if you like, you can review the content you missed in the “Review” icon below. Right now what is most important is that you jump back in here with us right now.';
					this.helper.presentToast(msg, this.data.status);
					this.navCtrl.push(WelcomeVideoPage);
					//this.navCtrl.setRoot(TabsPage);
				} else {
					this.helper.presentToast(this.data.msg, 'error');
				}
			},
			err => {
				this.loading.dismiss();
				this.helper.presentToast('Error while connecting to server.', 'error');
			}
		);
	}
	ionViewDidLoad() {
		this.checkCookie();
	}
	setCookie(username, password, exdays) {
		var d = new Date();
		d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
		var expires = "expires=" + d.toUTCString();
		document.cookie =
			username +
			"=" +
			this.loginForm.value.username +
			";" +
			expires +
			";path=/";
		document.cookie =
			password +
			"=" +
			this.loginForm.value.password +
			";" +
			expires +
			";path=/";
	}

	getCookie(cname) {
		let name = cname + "=";
		let ca = document.cookie.split(";");
		for (let i = 0; i < ca.length; i++) {
			let c = ca[i];
			while (c.charAt(0) == " ") {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return "";
	}

	checkCookie() {

		let username = this.getCookie("username");
		let password = this.getCookie("password");
		if (username != "") {
			this.loginForm.controls["username"].setValue(username);
			this.loginForm.controls["password"].setValue(password);
			this.loginForm.controls["remember_me"].setValue(true);
		} else {
			if (username != "" && username != null) {
				this.setCookie("username", "password", 365);
			}
		}
	}

	skip() {
		this.authService.clearNotificationCount().then(response => {
		})
		this.navCtrl.setRoot(ClassPage);
	}
}
