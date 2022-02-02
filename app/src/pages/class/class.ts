import { Component, ViewChild } from '@angular/core';
import { Storage } from '@ionic/storage';
import { Navbar, Events, IonicPage, LoadingController, NavController, NavParams, Platform } from 'ionic-angular';
import { AuthServiceProvider } from '../../providers/auth-service';
import { ClassServiceProvider } from '../../providers/class-service';
import { DataServiceProvider } from '../../providers/data.service';
import { Helper } from '../../providers/helper';
import { AudioPage } from '../audio/audio';
import { GeneralPage } from '../general/general';
import { HomePage } from '../home/home';
import { IntentionPage } from '../intention/intention';
import { NumberedGeneralPage } from '../numbered-general/numbered-general';
import { QuestionPage } from '../question/question';
import { TabsPage } from '../tabs/tabs';
import { TestimonialPage } from '../testimonial/testimonial';
import { TopicPage } from '../topic/topic';
import { VideoIntroPage } from '../video-intro/video-intro';


@IonicPage()
@Component({
	selector: 'page-class',
	templateUrl: 'class.html',
})
export class ClassPage {
	@ViewChild(Navbar) navBar: Navbar;
	result: any;
	classlist: any = [];
	loading: any;
	title: string = 'Course';
	msg: string = '';
	pages: Object = {
		GENERAL: GeneralPage,
		AUDIO: AudioPage,
		PODCAST: AudioPage,
		VIDEO: VideoIntroPage,
		TOPIC: TopicPage,
		TESTIMONIAL: TestimonialPage,
		INTENTION: IntentionPage,
		QUESTION: QuestionPage,
		NUMBERED_GENERAL: NumberedGeneralPage,
	};
	REVISIT_CLASS: boolean = false;
	class_title: string = '';
	bg_image: string = '';
	course_id: any = '';	
	ishidden = true;
	isDisabled: boolean = false;
	constructor(
		public loadCtrl: LoadingController,
		public navParams: NavParams,
		private classService: ClassServiceProvider,
		private authService: AuthServiceProvider,
		private dataService: DataServiceProvider,
		private navCtrl: NavController,
		public helper: Helper,
		private storage: Storage,
		public events: Events,
		public platform: Platform,	
	) {
		this.REVISIT_CLASS = false;
		this.classlist = [];
		this.course_id = '';
		this.authService.get_course_id().then(id => {
			this.course_id = id;
		});		
	}
	
	ionViewDidEnter() {
		this.isDisabled = false;
		this.events.subscribe('course:continueToPage', (settings) => {		
			this.ishidden = true;
			this.events.unsubscribe('course:continueToPage');		
			this.getPage(settings.class_id,settings.position);
		});		
	}

	// Check if class is loaded and go to the current page of class
	ionViewWillEnter() {
		this.classService.get_background_images().then(res => {
			let data: any = res;
			if (data.hasOwnProperty('inner_page')) {
				this.bg_image = data.inner_page;
			}
		});
		this.initClassPage();
	}	
	// Check to show class list or not
	initClassPage() {
		this.storage.get('course_settings').then((settings) => {
			let is_revisit;
			if (this.course_id) {
				is_revisit = (settings && settings[this.course_id]) ? settings[this.course_id]['CLASSES_RE-ENTERABLE'] == 1 : !1;
			}
			this.REVISIT_CLASS = is_revisit;
			setTimeout(() => {
				is_revisit ? this.classes() : this.startClass();
			}, 1000);
		});
	}

	// All classes
	classes() {
		this.ishidden = false;
		this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
		this.loading.present().then(() => {
			this.classService.classes().then(
				response => {
					this.loading && this.loading.dismiss();
					this.result = response;
					if (this.result.status == 'success') {
						this.classlist = this.result.data;
						let cls = this.classlist;
						if (cls.length > 2 && cls[0].start_at == cls[1].start_at && cls[0].class_status == 'STARTED') {
							this.classlist[1].status = 0;
						}
					}
				},
				err => {
					console.log(err);
					this.loading && this.loading.dismiss();
				}
			);
		});
	}

	startClassFromList(class_id) {
		this.isDisabled = true;
		this.getCurrentPosition(class_id);			
	}

	// Start current page for current class
	startClass() {		
		this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
		this.loading.present().then(() => {
			this.classService.getCurrentClass(this.course_id).then(
				response => {
					this.loading && this.loading.dismiss();
					this.result = response;
					if (this.result.status == 'success') {
						//let class_detail = this.result.data;
						let class_id = this.result.data.class_id;
						this.getCurrentPosition(class_id);
					} else if (this.result.status == 'NO_MORE_CLASS') {
						this.navCtrl.setRoot(TabsPage);
						//this.navCtrl.parent.select(0);
					}
				},
				err => {
					this.loading && this.loading.dismiss();
				}
			);
		});
	}

	// Get current page
	getPage(class_id, position) {
		let class_detail = { class_id: class_id, position: position };
		//this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
		//this.loading.present().then(() => {
		this.classService.getPage(class_detail).then(
			response => {
				this.result = response;
				if (this.result.status == 'success') {
					let page_detail = this.result.data;
					let pageType = page_detail.page_type;
					if (this.pages.hasOwnProperty(pageType)) {
						this.navCtrl.push(this.pages[pageType], { page_detail: page_detail, parent: this });
					}
				} else if (this.result.status == 'complete') {
					if (this.result.redirectTo == 'homework') {
						this.helper.presentToast(this.result.msg, 'success');
						this.dataService.changeClass(class_id);
						this.navCtrl.parent.select(4); // set homework page
					} else if (this.result.redirectTo == 'next_class') {
						this.startClass();
					} else {
						this.helper.presentToast(this.result.msg, 'success');
						this.navCtrl.parent.select(0);
					}
				} else if (this.result.status == 'error') {
					this.helper.presentToast(this.result.msg, 'error');
					this.navCtrl.parent.select(0);
				}
			},
			err => {
				// this.loading.dismiss();
			}
		);
		//})
	}

	// Get current page position for a class
	getCurrentPosition(class_id) {		
		this.classService.getCurrentPosition(class_id).then(
			response => {
				this.result = response;
				if (this.result.status == 'success') {
					let current_class = this.result.data;
					this.getPage(current_class.classes_id, current_class.current_page_position);
				} else if (this.result.status == 'INVALID_TOKEN') {
					this.helper.presentToast('Your session has been expired. Please log in to continue.', 'error');
					this.navCtrl.setRoot(HomePage);
				} else {
					this.getPage(class_id, 0);
				}
			},
			err => {
				//this.loading.dismiss();
			}
		);
	}
}