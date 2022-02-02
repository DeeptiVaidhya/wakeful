import { Component } from '@angular/core';
import { Storage } from '@ionic/storage';
import { IonicPage, LoadingController, NavController } from 'ionic-angular';
import { MenuController } from 'ionic-angular/components/app/menu-controller';
import { CONSTANTS } from '../../config/constants';
import { AuthServiceProvider } from '../../providers/auth-service';
import { ClassServiceProvider } from '../../providers/class-service';
import { DataServiceProvider } from '../../providers/data.service';
import { Helper } from '../../providers/helper';
import { HomeworkServiceProvider } from '../../providers/homework-service';
import { HomeworkDetailPage } from '../homework-detail/homework-detail';


/**
 * Generated class for the HomeworkPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */
@IonicPage()
@Component({
	selector: 'page-homework',
	templateUrl: 'homework.html',
})
export class HomeworkPage {
	REVISIT_CLASS: boolean = false;
	loading: any;
	homeworkList: any;
	shownGroup = null;
	result: any;
	title: string = "Homework";
	bg_image: string = '';
	course_id:any = '';
	classId: any = 0;
	constructor(private homeworkService: HomeworkServiceProvider, public loadCtrl: LoadingController, public navCtrl: NavController, public menu: MenuController, public helper: Helper, private storage: Storage, private authService: AuthServiceProvider, private classService: ClassServiceProvider, private dataService: DataServiceProvider ) {
		this.menu.enable(true);
		this.REVISIT_CLASS = false;
		this.course_id = '';
		this.authService.get_course_id().then(id => {
			this.course_id = id;
		});
		this.dataService.currentClass.subscribe(classId => {
			this.classId = classId;
		});
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

	getclassHomework() {
		this.loading = this.loadCtrl.create({ spinner: 'dots', content: 'Please wait...' });
		this.loading.present();
		this.homeworkService.homeworks().then(
			response => {
				this.loading.dismiss();
				this.result = response;
				if (this.result.status == 'success') {
					this.homeworkList = this.result.data;
				}
			},
			err => {
				var error = err.json();
				console.log(error);
			}
		);
	}


	homeworkDetail(exercise_detail) {
		exercise_detail.intro_text = exercise_detail.intro_text.replace(/\n/g, '<br>');
		this.navCtrl.push(HomeworkDetailPage, { 'exercise_detail': exercise_detail });
	}

	toggleGroup(group) {
		if (this.isGroupShown(group)) {
			this.shownGroup = null;
		} else {
			this.shownGroup = group;
		}
	};
	isGroupShown(group) {
		return this.shownGroup === group;
	};
	checkClass(group,homework){
		let flagFound = homework.classes_id == this.classId;
		flagFound && (this.shownGroup=group);
		return flagFound;
	}

	// Check to show class list or not
	initClassPage() {

		this.storage.get('course_settings').then((settings) => {
			if(this.course_id){
				let is_revisit = (settings && settings[this.course_id]) ? settings[this.course_id]['CLASSES_RE-ENTERABLE'] == 1 : !1;
				this.REVISIT_CLASS = is_revisit;
				setTimeout(() => {
					this.getclassHomework();
				}, 100);
			}else{
				let is_revisit = (settings && settings[CONSTANTS.CURRENT_COURSE]) ? settings[CONSTANTS.CURRENT_COURSE]['CLASSES_RE-ENTERABLE'] == 1 : !1;
				this.REVISIT_CLASS = is_revisit;
				setTimeout(() => {
					this.getclassHomework();
				}, 100);
			}
		})
	}
} 