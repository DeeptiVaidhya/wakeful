import { Component, ViewChild } from '@angular/core';
import { Navbar, IonicPage, NavParams, NavController, Tabs } from 'ionic-angular';
import { VideoPage } from '../video/video';
import { ClassPage } from '../class/class';
import { DashboardPage } from '../dashboard/dashboard';
import { ClassServiceProvider } from '../../providers/class-service';


/**
 * Generated class for the VideoIntroPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
	selector: 'page-video-intro',
	templateUrl: 'video-intro.html',
})
export class VideoIntroPage {
	parent: any;
	page_info: any = [];
	page_detail: any = [];
	video_page = VideoPage;
	@ViewChild(Navbar) navBar: Navbar;
	progress:any;
	bg_image:string='';
	disableButton : boolean = false;

	constructor(private classService: ClassServiceProvider,public navCtrl: NavController, private navParams: NavParams, private tab: Tabs) {
		this.page_info = this.navParams.get('page_detail');
		this.parent = this.navParams.get('parent');
		this.page_detail = this.page_info.page_data;
		this.progress = this.page_info.percentage;
	}

	playVideo() {
		this.navCtrl.push(VideoPage, { page_detail: this.page_info, parent: this });
	}

	ionViewWillEnter() {
		this.classService.get_background_images().then(res=>{
			let data:any=res;
			if(data.hasOwnProperty('inner_page')){
				this.bg_image = data.inner_page;
			}
		});
	}

	ionViewDidLoad() {
		this.previousPage();
	}

	ionViewWillUnload() {
		if (this.tab.getSelected().index != 1) {
			this.navCtrl.setRoot(DashboardPage);
			this.tab.select(0);
		} else {
			this.previousPage();
		}
	}

	previousPage() {
		var class_id = this.page_info.classes_id;
		var position = this.page_info.position;
		this.navBar.backButtonClick = () => {
			if (position == 0) {
				this.navCtrl.setRoot(ClassPage);
			} else {
				this.parent.getPage(class_id, +position - 1);
			}
		};
	}
	
	nextPage() {
		this.disableButton = true;
		var class_id = this.page_info.classes_id;
		var position = this.page_info.position;
		this.parent.getPage(class_id, +position + 1);
	}
}
