import { Component, ViewChild } from '@angular/core';
import { Navbar, IonicPage, NavController, NavParams, LoadingController } from 'ionic-angular';
import { ClassServiceProvider } from '../../providers/class-service';
import { Helper } from '../../providers/helper';
import { ClassPage } from '../class/class';

/**
 * Generated class for the NumberedGeneralPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-numbered-general',
  templateUrl: 'numbered-general.html',
})
export class NumberedGeneralPage {

	@ViewChild(Navbar) navBar: Navbar;
	page_info: any = [];
	page_detail: any = [];
	parent: any;
	loading: any;
	data: any;
	answer_data: any;
	answer: String = '';
	bg_image: String = '';
	progress: any;
	disableButton : boolean = false;

	constructor(public helper: Helper, private classService: ClassServiceProvider, public loadCtrl: LoadingController, public navCtrl: NavController, public navParams: NavParams) {
		this.page_info = this.navParams.get('page_detail');
		this.parent = this.navParams.get('parent');
		this.page_detail = this.page_info.page_data;
		this.progress = this.page_info.percentage;
	}

	ionViewWillEnter() {
		this.classService.get_background_images().then(res => {
			let data: any = res;
			if (data.hasOwnProperty('inner_page')) {
				this.bg_image = data.inner_page;
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
		this.parent.getPage(class_id, +position + 1);
	}
}
