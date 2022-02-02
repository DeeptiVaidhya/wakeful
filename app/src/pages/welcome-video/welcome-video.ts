import { Component } from '@angular/core';
import { IonicPage, LoadingController, NavController, NavParams, Platform} from 'ionic-angular';
import { TabsPage } from '../tabs/tabs';
import { AuthServiceProvider } from '../../providers/auth-service';

@IonicPage()
@Component({
  selector: 'page-welcome-video',
  templateUrl: 'welcome-video.html',
})
export class WelcomeVideoPage {
	loading: any;
	title: string = 'Welcome';

  constructor(public navCtrl: NavController, public navParams: NavParams,public loadCtrl: LoadingController,public platform: Platform,private authService: AuthServiceProvider,) {
  }

  ionViewDidLoad() {
    if (this.platform.is('ios')) {
      setTimeout(() => {
        var myVideo = document.getElementsByTagName('video')[0];
        myVideo.muted = true;
        if (myVideo.play()) {
          // setTimeout(() => {
          // 	// myVideo.muted = false;
          // }, 1000);
        }
      }, 1000);
    }
  }

videoEnd() {
	this.authService.check_login().then(status => {
		if (status) {
			this.navCtrl.setRoot(TabsPage);
		}
	}).catch(err => {
		console.log(err);
	});
}

}
