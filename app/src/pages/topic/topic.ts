import { Component, ViewChild } from '@angular/core';
import { IonicPage, LoadingController, ModalController, Navbar, NavController, NavParams } from 'ionic-angular';
import { ModalComponent } from '../../components/modal/modal';
import { ClassServiceProvider } from '../../providers/class-service';
import { ClassPage } from '../class/class';


/**
 * Generated class for the TopicPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
	selector: 'page-topic',
	templateUrl: 'topic.html',
})
export class TopicPage {
	@ViewChild(Navbar) navBar: Navbar;
	page_info: any = [];
	page_detail: any = [];
	parent: any;
	title: string = "Topic";
	bg_image: string = "";
	progress: any;
	disableButton : boolean = false;
	constructor(private classService: ClassServiceProvider, public modalCtrl: ModalController, public navCtrl: NavController, public navParams: NavParams, public loadCtrl: LoadingController) {
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

	showDetail(topic) {
		var color = topic.topic_color;
		var title = topic.topic_title;
		var script = topic.topic_text;
		let alert = this.modalCtrl.create(ModalComponent, { 'title': title, 'body': script, 'bgColor': color, 'type' : 'topic' });
		alert.present();
	}

	ionViewDidLoad() {
		this.previousPage();

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
		this.parent.getPage(class_id, +(position) + 1);
	}

}
