import { NgModule } from '@angular/core';

import { IonicPageModule } from 'ionic-angular';
import { AudioPage } from './audio';

@NgModule({
	declarations: [],
	imports: [IonicPageModule.forChild(AudioPage)], //VgCoreModule, VgControlsModule, VgOverlayPlayModule, VgBufferingModule,
	bootstrap: [AudioPage],
	entryComponents: [],
})
export class AudioPageModule {}
