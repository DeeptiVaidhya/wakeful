import { Component } from '@angular/core';
import { Storage } from '@ionic/storage';
import { IonicPage, LoadingController, NavController, NavParams } from 'ionic-angular';
import { MenuController } from 'ionic-angular/components/app/menu-controller';
import { CONSTANTS } from '../../config/constants';
import { AuthServiceProvider } from '../../providers/auth-service';
import { ClassServiceProvider } from '../../providers/class-service';
import { CommunityServiceProvider } from '../../providers/community-service';
import { Helper } from '../../providers/helper';
import { CommunityDiscussionPage } from '../community-discussion/community-discussion';


/**
 * Generated class for the HomeworkPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */
@IonicPage()
@Component({
	selector: 'page-community',
	templateUrl: 'community.html',
})
export class CommunityPage {
	REVISIT_CLASS: boolean = false;
	loading: any;
	communityList: any;
	class_id:number;
	shownGroup = null;
	result: any;
	title: string = "Community";
	breadcrumb: any = [];
	bg_image: string = '';
	course_id:any = '';
	constructor(private communityService: CommunityServiceProvider, public loadCtrl: LoadingController, public navCtrl: NavController, public menu: MenuController, public helper: Helper, private storage: Storage, private authService: AuthServiceProvider, private classService: ClassServiceProvider, public navParams: NavParams,) {
		this.menu.enable(true);
		this.REVISIT_CLASS = false;
		this.breadcrumb = ['Community', 'Discussion'];
		this.course_id = '';
		this.authService.get_course_id().then(id => {
			this.course_id = id;
    });
    this.class_id = this.navParams.get('class_id');
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

	// getting community list
	getcommunityList() {
		this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
		this.loading.present();
		this.communityService.communities(10).then(
			response => {
				this.loading.dismiss();
				this.result = response;
				if (this.result.status == 'success') {
					this.communityList = this.result.data;
				}
			},
			err => {
				this.loading.dismiss();
			}
		);

	}

	// Community discussion page
	getCommunityTopic(question_detail) {
		this.navCtrl.push(CommunityDiscussionPage, { 'question_detail': question_detail });
	}

	// open close accordion
	toggleGroup(group) {
		if (this.isGroupShown(group)) {
			this.shownGroup = null;
		} else {
			this.shownGroup = group;
		}
	}


	// check for current community
	isGroupShown(group) {
		return this.shownGroup === group;
	};


	// Check to show class list or not
	initClassPage() {

		this.storage.get('course_settings').then((settings) => {
			if(this.course_id){
				let is_revisit = (settings && settings[this.course_id]) ? settings[this.course_id]['CLASSES_RE-ENTERABLE'] == 1 : !1;
				this.REVISIT_CLASS = is_revisit;
				setTimeout(() => {
					this.getcommunityList();
				}, 100);
			}else{
				let is_revisit = (settings && settings[CONSTANTS.CURRENT_COURSE]) ? settings[CONSTANTS.CURRENT_COURSE]['CLASSES_RE-ENTERABLE'] == 1 : !1;
				this.REVISIT_CLASS = is_revisit;
				setTimeout(() => {
					this.getcommunityList();
				}, 100);
			}
		})
	}



}
