import { Component } from '@angular/core';
import { IonicPage, NavController } from 'ionic-angular';
// import { HomePage } from '../home/home';
import { Helper } from '../../providers/helper';

/**
 * Generated class for the OverviewPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
	selector: 'page-overview',
	templateUrl: 'overview.html',
})
export class OverviewPage {
	data: any = [];
	constructor(public navCtrl: NavController, public helper: Helper) {
		this.data = [
			{
				title: 'What is Mindfulness?',
				desc:'While many definitions exist, a basic description of mindfulness is simply “paying attention to the present moment without judgment.” This entails training our attention to notice what is happening from moment-to-moment, and to observe whatever is arising in our thoughts, emotions, and physical sensations with openness, curiosity, and non-judgment.',
				showDetails: false,
				icon: 'ios-arrow-down',
			},
			{
				title: 'What types of activities will I complete in the Wakeful™ tool?',
				desc:'Throughout this course, you will learn different mindfulness meditation and mindful movement practices, as well as other exercises designed to teach you foundational skills of mindfulness.',
				icon: 'ios-arrow-down',
				showDetails: false,
			},
			{
				title: 'How long does the course take?',
				desc:'The full recommended Wakeful™ program begins with a brief orientation session, followed by 9 consecutive classes, one per week. Each class is designed to take no more than 90 minutes, however this can be broken into smaller segments if necessary. The only difference is with Class 7, where you will be asked to participate in an extended period of guided mindful mediation and mindful movement practices (± 3 hours), which can help deepen your practice and enhance your skills. Because certain professional partners we collaborate with have the ability to tailor the number of classes offered, it is possible that you are enrolled in a Wakeful™ program that is not the full 9-week course. If you are uncertain, you can see the “Journey” icon in the dashboard of the app to see how many classes you have been assigned.',
				showDetails: false,
				icon: 'ios-arrow-down',
			},			

			{
				title: 'Will I need to do anything outside of class?',
				desc:'Yes. Each week you will find dedicated guided meditations and practices as well as integrated mindful activities in the Practice Section. Independently practicing mindfulness in between classes will help you to get the most out of Wakeful.',
				showDetails: false,
				icon: 'ios-arrow-down',
			},

			{
				title: 'Can I still use the Wakeful™ tool after my course has finished?',
				desc:'Yes, once you have finished your course, you will still have access to guided mindfulness recordings and educational content from the classes. You will also be able to use our meditation timer to support your practice.',
				showDetails: false,
				icon: 'ios-arrow-down',
			},

		];
	}

	toggleDetails(data) {
		if (data.showDetails) {
			data.showDetails = false;
			data.icon = 'ios-arrow-down';
		} else {
			data.showDetails = true;
			data.icon = 'ios-arrow-up';
		}
	}
	ionViewCanEnter() {
		// this.helper.authenticated().then(
		// 	response => {
		// 	},
		// 	err => {
		// 	}
		// );
	}
}
