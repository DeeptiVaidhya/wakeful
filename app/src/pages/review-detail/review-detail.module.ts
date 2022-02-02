import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { ReviewDetailPage } from './review-detail';
import { VgCoreModule } from 'videogular2/core';
import { VgControlsModule } from 'videogular2/controls';
import { VgOverlayPlayModule } from 'videogular2/overlay-play';
import { VgBufferingModule } from 'videogular2/buffering';

@NgModule({
	declarations: [
		ReviewDetailPage,
	],
	imports: [VgCoreModule, VgControlsModule, VgOverlayPlayModule, VgBufferingModule, IonicPageModule.forChild(ReviewDetailPage)],
	bootstrap: [ReviewDetailPage]	
})
export class ReviewDetailPageModule {} 
