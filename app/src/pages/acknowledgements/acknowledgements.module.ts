import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { AcknowledgementsPage } from './acknowledgements';

@NgModule({
  declarations: [
    AcknowledgementsPage,
  ],
  imports: [
    IonicPageModule.forChild(AcknowledgementsPage),
  ],
})
export class AcknowledgementsPageModule {}
