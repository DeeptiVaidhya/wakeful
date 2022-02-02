import { Component } from '@angular/core';
import { Events, IonicPage, LoadingController, NavController } from 'ionic-angular';
import { MenuController } from 'ionic-angular/components/app/menu-controller';
import { CONSTANTS } from '../../config/constants';
import { AuthServiceProvider } from '../../providers/auth-service';
import { ClassServiceProvider } from '../../providers/class-service';
import { Helper } from '../../providers/helper';
import { ClassSchedulePage } from '../class-schedule/class-schedule';

/**
 * Generated class for the DashboardPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
	selector: 'page-dashboard',
	templateUrl: 'dashboard.html',
})

export class DashboardPage {

	title: string = 'Dashboard';
	loading: any;
	bg_image: string='';
	result: any;
	rootPage: any;
	course_id:any = '';
	JOURNEY_ENABLED:any;
	tabshow:boolean = true;
	access_page_id:any=0;
	page_status = 'DASHBOARD';
	resource_id:any;
	spent_time:any=0;
	dashboardData: Object = {
		completed_class: 0,
		consecutive_days: 0,
		meditation_minutes: 0,
		username: 'N/A',
		current_class_id: 0,
		current_class: 'N/A',
		class_status: '',
		current_page: 'N/A',
		current_page_id: 0,
		current_class_status: '',
		is_new_user: false,
		week_number:0,
		nextClassDays:0
	};

	constructor(
		public menu: MenuController,
		private classService: ClassServiceProvider,
		private authService: AuthServiceProvider,
		private loadCtrl: LoadingController,
		public navCtrl: NavController,
		public helper: Helper,
		private events:Events,
	) {
		this.events.subscribe('course:updateSettings', (settings) => {
			this.initClassPage(settings);
		});
	}

	getDashboardData() {
		this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
		this.loading.present();
		this.classService.dashboard(this.course_id).then(
			response => {
				this.loading.dismiss();
				this.result = response;
				if (this.result.status == 'success' && this.result.data) {
					let data = this.result.data;
					this.dashboardData['completed_class'] = data.completed_class;
					this.dashboardData['consecutive_days'] = data.user_profile.consecutive_days;
					this.dashboardData['meditation_minutes'] = data.meditation_minutes;
					this.dashboardData['username'] = data.user_profile.username;
					this.dashboardData['is_new_user'] = data.is_new_user;

					if(data.current_class && data.current_class.hasOwnProperty('class_id')){
						this.dashboardData['current_class'] =data.current_class.title;
						this.dashboardData['current_class_id'] =data.current_class.class_id;
						this.dashboardData['class_status'] =data.current_class.status;
						this.dashboardData['current_class_status'] =data.current_class_status;
						this.dashboardData['week_number'] = data.current_class.week_number;
						this.dashboardData['nextClassDays'] = data.next_class_day; 
						this.dashboardData['position'] = data.current_page.position
					}

					if(data.current_page && data.current_page.hasOwnProperty('id')){
						this.dashboardData['current_page'] =data.current_page.title;
						this.dashboardData['current_page_id'] =data.current_page.id;
					}
				}
			},
			err => {
				this.loading.dismiss();
			}
		);
	}

	checkTabEnabled(){
		this.authService.check_user_class_count({'course_id': this.course_id}).then(res => {
			if(res['status'] == 'success'){
				this.tabshow = false;
			} else {
				this.tabshow = true;
			}
		})
	}

	startClass() {
		this.events.publish('course:continueToPage',{class_id:this.dashboardData['current_class_id'],position:this.dashboardData['position']});
		this.navCtrl.parent.select(0);	
	}

	classSchedule(){
		this.navCtrl.setRoot(ClassSchedulePage);
	}

	ionViewWillEnter() {
		let total = 0;
		this.access_page_id = 0;
		let resource_info = {
			resource_id : this.access_page_id,
			spent_time: total,
			status: this.page_status
		};	
		this.authService.get_course_id().then(id => {
			this.course_id = id;
			this.getDashboardData();
			this.checkTabEnabled();
			this.onAccessPage(resource_info);
		});
	}

	onAccessPage(resource_info) {
		this.authService.accessedResources(resource_info).then(
			result => {				
				this.access_page_id = result['data']['resource_id'];
			},
			err => {console.log(err);}
		);
	}

	ionViewDidLeave() {
		let resource_info = {
			resource_id : this.access_page_id,
			spent_time: this.spent_time,
			status: this.page_status
		};	
		this.onAccessPage(resource_info);
		this.access_page_id=0;	
	}

	initClassPage(settings) {
		this.authService.get_course_id().then(id => {
			let course_id:any = id;
			let journey_enabled;
			if(course_id){
				journey_enabled = (settings && settings[course_id]) ? settings[course_id]['JOURNEY_PAGE_VIEWABLE'] == 1 : 0;
			} else{
				journey_enabled = (settings && settings[CONSTANTS.CURRENT_COURSE]) ? settings[CONSTANTS.CURRENT_COURSE]['JOURNEY_PAGE_VIEWABLE'] == 1 : 0;
			}
			this.JOURNEY_ENABLED = journey_enabled;
		});
	}
}
