import { Component, ViewChild } from '@angular/core';
import { IonicPage, LoadingController, Navbar, NavController, NavParams } from 'ionic-angular';
import { ClassServiceProvider } from '../../providers/class-service';
import { ClassPage } from '../class/class';
/**
 * Generated class for the GeneralPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
	selector: 'page-general',
	templateUrl: 'general.html',
})

export class GeneralPage {
	@ViewChild(Navbar) navBar: Navbar;
	page_info: any = [];
	page_detail: any = [];
	loading: any;
	result: any;
	parent: any;
	title: string = "Test";
	bg_image: string = "";
	isShownImage: boolean = false;
	progress:any;
	comunity : boolean = false;
	disableButton : boolean = false;

	constructor(
		public navCtrl: NavController,
		private navParams: NavParams,
		public loadCtrl: LoadingController,
		private classService: ClassServiceProvider,
	) {
		this.page_info = this.navParams.get('page_detail');
		this.parent = this.navParams.get('parent');
		this.page_detail = this.page_info.page_data;
		this.progress = this.page_info.percentage;
	}


	ionViewWillEnter() {
		this.classService.get_background_images().then(res=>{
			let data:any=res;
			let imgType = this.page_detail.remove_foreground_objects=='1' ? 'inner_page' : 'main_page';
			if(data.hasOwnProperty(imgType)){
				this.bg_image = data[imgType];
			}
		});
	}

	ionViewDidLoad() {
		this.previousPage()
	}

	previousPage() {
		var class_id = this.page_info.classes_id;
		var position = this.page_info.position;
		this.navBar.backButtonClick = () => {
			if (position == 0) {
				this.navCtrl.setRoot(ClassPage);
			} else {
				this.parent.getPage(class_id, +(position) - 1);
			}
		}
	}

	nextPage() {
		this.disableButton = true;
		var class_id = this.page_info.classes_id;
		var position = this.page_info.position;
		this.parent.getPage(class_id, + position + 1);
	}

	practicePage() {
		this.disableButton = true;
		this.navCtrl.parent.select(4); 
	}

	communityPage() {
		//this.navCtrl.setRoot(CommunityUserPage);
		this.disableButton = true;
		this.navCtrl.parent.select(3); 
	}
}
