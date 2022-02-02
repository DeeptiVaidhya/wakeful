import { Component } from '@angular/core';
import { IonicPage, NavController, NavParams } from 'ionic-angular';
import { ClassServiceProvider } from '../../providers/class-service';
import { Helper } from '../../providers/helper';
/**
 * Generated class for the ClassSchedulePage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-class-schedule',
  templateUrl: 'class-schedule.html',
})
export class ClassSchedulePage {
  
  title: string = 'Class Schedule';
  breadcrumb = ['Dashboard', 'Class Schedule'];
  classList: any;
  constructor(public navCtrl: NavController, public navParams: NavParams, private classService: ClassServiceProvider, public helper: Helper) {
  }

  ionViewWillEnter(){
    this.classService.classList().then(res=>{
      if(res['status'] == 'success'){
        this.classList = res['data'];
      } else {
        this.helper.presentToast(res['msg'], 'error');
      }      
		});
  }

}
