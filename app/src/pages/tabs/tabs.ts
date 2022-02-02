import { Component, ViewChild } from '@angular/core';
import { CONSTANTS } from '../../config/constants';
import { HomePage } from '../home/home';
import { DashboardPage } from '../dashboard/dashboard';
import { ClassPage } from '../class/class';
import { ReviewPage } from '../review/review';
import { MeditationTimerPage } from '../meditation-timer/meditation-timer';
import { CommunityUserPage } from '../community-user/community-user';
import { AuthServiceProvider } from '../../providers/auth-service';
import { Helper } from '../../providers/helper';
import { Storage } from '@ionic/storage';

import { NavController, Tabs,Events } from 'ionic-angular';

//import { dashCaseToCamelCase } from '@angular/compiler/src/util';

@Component({
	templateUrl: 'tabs.html',
})
export class TabsPage {
	tab1Root = ClassPage;
	tab2Root = DashboardPage;
	tab3Root = ReviewPage;
	tab4Root = CommunityUserPage;
	tab5Root = MeditationTimerPage;
	tab6Root = MeditationTimerPage;
	rootPage:any;
	JOURNEY_ENABLED:any;
	tabshow:boolean = true;
	@ViewChild('imTabs') tabRef: Tabs;

	hiddenTabs:false;
	uriSegment='';

	constructor(private authService: AuthServiceProvider, private navCtrl: NavController, public helper: Helper,private events:Events,private storage: Storage) {

		this.events.subscribe('homework:disableTabs', (checkHidden)=>{
			this.hiddenTabs = checkHidden;
		});
		this.events.subscribe('course:updateSettings', (settings) => {
			// user and time are the same arguments passed in `events.publish(user, time)`
			this.initClassPage(settings);
		});
		if(!!window.location.href.split('#')[1]){
			this.uriSegment = window.location.href.split('#')[1].replace(/\//g,'');
		} else {
			this.uriSegment = 'course';
		}
		
		this.storage.get('isvideoShow').then(isvideoShow => {
			let tabsBar = document.getElementById('tabsBar');
			if (tabsBar && !isvideoShow) {
				tabsBar.classList.add('hidden');
				// this.hiddenTabs = isvideoShow;	
			}
		})
	}
	
	// isClassPage(){
	// 	if(this.tabRef.getSelected()){
	// 		return this.tabRef.getSelected().index==1;
	// 	}
	// 	return false;
	// }
	// Check for valid token on every tab click
	check_login() {
		if(!!window.location.href.split('#')[1]){
			this.uriSegment = window.location.href.split('#')[1].replace(/\//g,'');
		} else {
			this.uriSegment = 'course';
		}
		this.authService.check_login().then(status => {
			if (!status) {
				this.helper.presentToast('Your session has been expired. Please log in to continue.', 'error');
				this.navCtrl.setRoot(HomePage);
			} else {
				this.events.publish('user:loggedin');
			}
		});
		switch(this.uriSegment){
			case "course":
				this.tabRef.select(0);
				break;
			case "dashboard":
				this.tabRef.select(1);
				break;
			case "review":
				this.tabRef.select(2);
				break;
			case "community":
				this.tabRef.select(3);
				break;
			case "homework":
				this.tabRef.select(4);
				break;
			case "meditation-timer":
				this.tabRef.select(5);
				break;
			
		}
		this.uriSegment='';
	}
	initClassPage(settings) {
		let journey_enabled = (settings && settings[CONSTANTS.CURRENT_COURSE]) ? settings[CONSTANTS.CURRENT_COURSE]['JOURNEY_PAGE_VIEWABLE'] == 1 : 0;
		this.JOURNEY_ENABLED = journey_enabled;
		this.authService.get_course_id().then(id => {
			let course_id:any = id;
			// let journey_enabled;
			if(course_id){
				journey_enabled = (settings && settings[course_id]) ? settings[course_id]['JOURNEY_PAGE_VIEWABLE'] == 1 : 0;
				// this.JOURNEY_ENABLED = journey_enabled;
				// this.authService.check_user_class_count({'course_id': course_id}).then(res => {
				// 	if(res['status'] == 'success'){
				// 		this.tabshow = false;
				// 	}else{
				// 		this.tabshow = true;
				// 	}
				// })

			} else{
				// journey_enabled = (settings && settings[CONSTANTS.CURRENT_COURSE]) ? settings[CONSTANTS.CURRENT_COURSE]['JOURNEY_PAGE_VIEWABLE'] == 1 : 0;
				// this.JOURNEY_ENABLED = journey_enabled;
				//  this.authService.check_user_class_count(CONSTANTS.CURRENT_COURSE).then(res => {
				// 	if(res['status'] == 'success'){
				// 		this.tabshow = false;
				// 	}else{
				// 		this.tabshow = true;
				// 	}
				// })
			}
			this.JOURNEY_ENABLED = journey_enabled;
			this.authService.check_user_class_count(course_id?{'course_id': course_id}:CONSTANTS.CURRENT_COURSE).then(res => {
				if(res['status'] == 'success'){
					this.tabshow = false;
				}else{
					this.tabshow = true;
				}
			});
		});
	}

}
