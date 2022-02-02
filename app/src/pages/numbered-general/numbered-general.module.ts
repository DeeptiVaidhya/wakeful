import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { NumberedGeneralPage } from './numbered-general';

@NgModule({
  declarations: [
    NumberedGeneralPage,
  ],
  imports: [
    IonicPageModule.forChild(NumberedGeneralPage),
  ],
})
export class NumberedGeneralPageModule {}
