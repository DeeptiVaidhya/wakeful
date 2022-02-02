import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { AboutPage } from './about';
import { VgCoreModule } from 'videogular2/core';
import { VgControlsModule } from 'videogular2/controls';
import { VgOverlayPlayModule } from 'videogular2/overlay-play';
import { VgBufferingModule } from 'videogular2/buffering';

@NgModule({
	declarations: [AboutPage],
	imports: [VgCoreModule, VgControlsModule, VgOverlayPlayModule, VgBufferingModule, IonicPageModule.forChild(AboutPage)],
})
export class AboutPageModule {}
