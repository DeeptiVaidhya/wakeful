import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { TopicPage } from './topic';
//import { AppHeaderComponent } from '../../components/app-header/app-header';

@NgModule({
	declarations: [TopicPage],
	imports: [IonicPageModule.forChild(TopicPage)],
	//exports: [AppHeaderComponent]
	
})
export class TopicPageModule {}
