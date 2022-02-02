import { Component, ViewChild } from '@angular/core';
import { Navbar, IonicPage, NavController, NavParams, Slides } from 'ionic-angular';
//import { CONSTANTS } from '../../config/constants';
import { ClassPage } from '../class/class';
import { ClassServiceProvider } from '../../providers/class-service';


/**
 * Generated class for the TestimonialPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
	selector: 'page-testimonial',
	templateUrl: 'testimonial.html',
})
export class TestimonialPage {
	@ViewChild(Slides) slides: Slides;
	@ViewChild(Navbar) navBar: Navbar;
	page_info: any = [];
	page_detail: any = [];
	parent: any;
	progress:any;
	bg_image:String = '';
	disableButton : boolean = false;

	constructor(public navCtrl: NavController, public navParams: NavParams,private classService: ClassServiceProvider) {
		this.page_info = this.navParams.get('page_detail');
		this.parent = this.navParams.get('parent');
		this.page_detail = this.page_info.page_data;
		this.progress = this.page_info.percentage;
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


	// Method that shows the next slide
	public slideNext(): void {
		this.slides.slideNext();
	}

	// Method that shows the previous slide
	public slidePrev(): void {
		this.slides.slidePrev();
	}
}
