import { Component, ViewChild } from '@angular/core';
import { SplashScreen } from '@ionic-native/splash-screen';
import { StatusBar } from '@ionic-native/status-bar';
import { Storage } from '@ionic/storage';
import { DEFAULT_INTERRUPTSOURCES, Idle } from '@ng-idle/core';
import { AlertController, Events, NavController, Platform } from 'ionic-angular';
import { MenuController } from 'ionic-angular/components/app/menu-controller';
import { CONSTANTS } from '../config/constants';
import { AboutPage } from '../pages/about/about';
import { ContactPage } from '../pages/contact/contact';
import { FeedbackPage } from '../pages/feedback/feedback';
import { HomePage } from '../pages/home/home';
import { OverviewPage } from '../pages/overview/overview';
import { ProfilePage } from '../pages/profile/profile';
import { ResetPasswordPage } from '../pages/reset-password/reset-password';
import { TabsPage } from '../pages/tabs/tabs';
import { AuthServiceProvider } from '../providers/auth-service';
import { ClassServiceProvider } from '../providers/class-service';
import { Helper } from '../providers/helper';
import { NotificationPage } from '../pages/notification/notification';

@Component({
	templateUrl: 'app.html',
	providers: [AuthServiceProvider],
})
export class MyApp {
	public rootPage;
	user: any = false;
	isLoggedIn: any = false;
	notificationCount = 0;
	storage: any = null;
	alerttitle: any;
	alertmessage: any;
	interval: any;
	data: any;
	code: String = '';
	bg_image: String = '';
	@ViewChild('imhereNav') navCtrl: NavController;

	idleState = 'Not started.';
	timedOut = false;
	lastPing?: Date = null;

	constructor(
		platform: Platform,
		statusBar: StatusBar,
		splashScreen: SplashScreen,
		storage: Storage,
		private authService: AuthServiceProvider,
		private classService: ClassServiceProvider,
		private alertCtrl: AlertController,
		public menu: MenuController,
		public events: Events,
		// public socialService: AuthService,
		public helper: Helper,
		private idle: Idle
	) {
		this.storage = storage;
		platform.ready().then(() => {
			// Okay, so the platform is ready and our plugins are available.
			// Here you can do any higher level native things you might need.
			statusBar.styleDefault();
			splashScreen.hide();
			this.events.subscribe('user:notification', () => {
				// notification count subscribe
				this.storage.get('notification_count').then(notificationCount => {
					this.notificationCount = notificationCount ;
				});
			});
			this.storage.get('token').then(token => {
				this.isLoggedIn = !!token;
				if (token) {
					this.rootPage = TabsPage;
				} else {
					 this.code = location.search.split('code=')[1];
					 let url = document.URL.split('#')[1];
					 let slug = '', accesscode = '';
					 if(url != undefined){
							slug = url.split('/')[1];
							accesscode = url.split('/')[2];
					 }
					if (this.code != undefined) {
						this.navCtrl.setRoot(ResetPasswordPage, { code: this.code });
					} else if(slug == 'sign-up' && url != undefined) {
						// Don't remove this condition. This is used for hold url to signup page for create password
						// this.navCtrl.setRoot(SignupPage, { accesscode: accesscode });
					} else {
						this.rootPage = HomePage;
				  }
				}
				clearInterval(this.interval);
				this.interval = setInterval(() => {
					if (this.isLoggedIn) {
						this.authService.getNotificationCount().then(response=>{
							this.storage.set('notification_count', response['notification_count']);
							events.publish('user:notification');
							
						});
					}
				}, 10000);
			});
			this.classService.set_background_images();
		});
		events.subscribe('user:loggedin', () => {
			// user and time are the same arguments passed in `events.publish(user, time)`
			this.isLoggedIn = !0;
			this.reset();
		});
		// sets an idle timeout of 5 seconds, for testing purposes.
		idle.setIdle(CONSTANTS.SESSION_TIMEOUT);//
		// sets a timeout period of 5 seconds. after 10 seconds of inactivity, the user will be considered timed out.
		idle.setTimeout(5);
		// sets the default interrupts, in this case, things like clicks, scrolls, touches to the document
		idle.setInterrupts(DEFAULT_INTERRUPTSOURCES);

		//idle.onIdleEnd.subscribe(() => (this.idleState = 'No longer idle.'));
		idle.onTimeout.subscribe(() => {
			this.idleState = 'Timed out!';
			this.timedOut = true;
		});
		idle.onIdleStart.subscribe(() => {
			this.idleState = "You've gone idle!";
			this.isLoggedIn = false;
			this.storage.clear();
			this.timedOut = true;
			this.helper.presentToast('Your session has been expired. Please log in to continue.', 'error');
			this.navCtrl.setRoot(HomePage);
		});

	}

	reset() {
		this.idle.watch();
		this.idleState = 'Started.';
		this.timedOut = false;
	}

	overview() {
		this.navCtrl.setRoot(OverviewPage);
	}

	notification() {
		this.authService.clearNotificationCount().then(response => {
		})
		this.navCtrl.setRoot(NotificationPage);
	}

	about() {
		this.navCtrl.setRoot(AboutPage);
	}

	contact() {
		this.navCtrl.setRoot(ContactPage);
	}

	feedback() {
		this.navCtrl.setRoot(FeedbackPage);
	}

	profile() {
		this.navCtrl.setRoot(ProfilePage);
	}

	logout() {
		this.authService.logout().then(
			result => {
				this.data = result;
				if (this.data.status == 'success') {
					this.isLoggedIn = !1;
					this.storage.clear();
					// this.socialService.authState.subscribe(user => {
					// 	if (user != null) {
					// 		this.socialService.signOut();
					// 	}
					// });
					this.reset();
					this.navCtrl.setRoot(HomePage); // go to Home
				} else {
					this.alerttitle = 'Error';
					this.alertmessage = this.data.msg;
					this.doAlert();
				}
			},
			err => {}
		);
	}

	doAlert() {
		let alert = this.alertCtrl.create({
			title: this.alerttitle,
			subTitle: this.alertmessage,
			buttons: ['Dismiss'],
		});
		alert.present();
	}
}
