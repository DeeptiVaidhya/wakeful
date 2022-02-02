import { Component, ViewChild} from '@angular/core';
import { Storage } from '@ionic/storage';
import { IonicPage, NavController, Platform, Slides } from 'ionic-angular';
import { ClassServiceProvider } from '../../providers/class-service';
import { SigninPage } from '../signin/signin';
import { SignupPage } from '../signup/signup';
import { TabsPage } from '../tabs/tabs';


//import { CONSTANTS } from '../../config/constants';

@IonicPage()
@Component({
	selector: 'page-home',
	templateUrl: 'home.html',
})
export class HomePage {
	slideData: any;
	title: any = '';
	bg_image: any = '';
	public rootPage;
	signInPage = SigninPage;
	signUpPage = SignupPage;

	@ViewChild('slides') slides: Slides;

	constructor(
		public navCtrl: NavController,
		private storage: Storage,
		public platform: Platform,
		public classService: ClassServiceProvider
	) {
		this.storage.get('token').then(token => {
			if (token) {
				this.rootPage = TabsPage;
			} else {
				this.rootPage = HomePage;
			}
		});
	}
	ionViewWillEnter() {
		this.classService.get_background_images().then(res => {
			let data: any = res;
			if (res.hasOwnProperty('main_page')) {
				this.bg_image = data.main_page;
			}
		});

	}
	signIn(){
		this.navCtrl.push(SigninPage, {course: 'course'})
	}
}
