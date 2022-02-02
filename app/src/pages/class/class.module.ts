import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { ClassPage } from './class';
import { GeneralPage } from '../general/general';
import { AudioPage } from '../audio/audio';
import { ProgressBarComponent } from '../../components/progress-bar/progress-bar';

@NgModule({
	declarations: [
		ClassPage, ProgressBarComponent
	],
	imports: [
		IonicPageModule.forChild(ClassPage),
		IonicPageModule.forChild(GeneralPage),
		IonicPageModule.forChild(AudioPage),
	],
	exports: [ProgressBarComponent]
})
export class ClassPageModule { }
