import { Component } from '@angular/core';
import { NavController, LoadingController, IonicPage } from 'ionic-angular';
import { ReviewServiceProvider } from '../../providers/review-service';
import { ClassServiceProvider } from '../../providers/class-service';
import { AuthServiceProvider } from '../../providers/auth-service';
import { Helper } from '../../providers/helper';
import { MenuController } from 'ionic-angular/components/app/menu-controller';
import { ReviewDetailPage } from '../review-detail/review-detail';
import { CONSTANTS } from '../../config/constants';
import { Storage } from '@ionic/storage';

/**
 * Generated class for the ReviewPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */
@IonicPage()
@Component({
	selector: 'page-review',
	templateUrl: 'review.html',
})
export class ReviewPage {
	REVISIT_CLASS: boolean = false;
	reviewlist: any;
	loading: any;
	result: any;
	title: string = "Review";
	bg_image: string = '';
	course_id:any = '';
	players:any=[];
	isDisabled: boolean = false;
	access_page_id:any=0;
	spent_time:any=0;
	page_status = 'REVIEW';
	resource_id:any;
	constructor(
		private reviewService: ReviewServiceProvider,
		public loadCtrl: LoadingController,
		public navCtrl: NavController,
		public menu: MenuController,
		public helper: Helper,
		private storage: Storage,
		private authService: AuthServiceProvider,
		private classService: ClassServiceProvider
	) {
		this.menu.enable(true);
		this.REVISIT_CLASS = false;
		this.course_id = '';
		this.authService.get_course_id().then(id => {
			this.course_id = id;
		});

	}

	ionViewDidEnter() {
		this.isDisabled = false;
		let total = 0;
		this.access_page_id = 0;
		let resource_info = {
			resource_id : this.access_page_id,
			spent_time: total,
			status: this.page_status
		};
		this.onAccessPage(resource_info);
	}

	getclassReview() {
		this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
		this.loading.present();
		this.reviewService.reviews().then(
			response => {
				this.loading.dismiss();
				this.result = response;
				if (this.result.status == 'success') {
					this.reviewlist = this.result.data;
				}
			},
			err => {
				var error = err.json();
				console.log(error);
			}
		);
	}

	onAccessPage(resource_info) {
		this.authService.accessedResources(resource_info).then(
			result => {				
				this.access_page_id = result['data']['resource_id'];
			},
			err => {console.log(err);}
		);
	}

	reviewDeatil(id) {		
		this.isDisabled = true;
		this.navCtrl.push(ReviewDetailPage, { review_id: id });
	}


	ionViewWillEnter() {
		this.classService.get_background_images().then(res=>{
			let data:any=res;
			if(data.hasOwnProperty('inner_page')){
				this.bg_image = data.inner_page;
			}
		});
		this.initClassPage();
	}

	ionViewDidLeave() {
		let resource_info = {
			resource_id : this.access_page_id,
			spent_time: 0,
			status: this.page_status
		};	
		this.onAccessPage(resource_info);
		this.access_page_id=0;	
	}


	// Check to show class list or not
	initClassPage() {
		this.storage.get('course_settings').then((settings) => {
			if(this.course_id){
				let is_revisit = (settings && settings[this.course_id]) ? settings[this.course_id]['CLASSES_RE-ENTERABLE'] == 1 : !1;
				this.REVISIT_CLASS = is_revisit;
				setTimeout(() => {
					this.getclassReview();
				}, 100);
			}else{
				let is_revisit = (settings && settings[CONSTANTS.CURRENT_COURSE]) ? settings[CONSTANTS.CURRENT_COURSE]['CLASSES_RE-ENTERABLE'] == 1 : !1;
				this.REVISIT_CLASS = is_revisit;
				setTimeout(() => {
					this.getclassReview();
				}, 100);
			}
		})
	}

}
