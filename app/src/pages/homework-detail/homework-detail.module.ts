import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { HomeworkDetailPage } from './homework-detail';
import { VgCoreModule } from 'videogular2/core';
import { VgControlsModule } from 'videogular2/controls';
import { VgOverlayPlayModule } from 'videogular2/overlay-play';
import { VgBufferingModule } from 'videogular2/buffering';

@NgModule({
	declarations: [
		HomeworkDetailPage,
	],
	imports: [VgCoreModule, VgControlsModule, VgOverlayPlayModule, VgBufferingModule, IonicPageModule.forChild(HomeworkDetailPage)],
	bootstrap: [HomeworkDetailPage]
})
export class HomeworkDetailPageModule { } 
