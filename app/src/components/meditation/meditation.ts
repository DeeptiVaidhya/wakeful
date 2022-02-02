import { Component,OnInit,OnDestroy } from '@angular/core';
import { LoadingController,ViewController,NavController, NavParams } from 'ionic-angular';
// import { ModalComponent } from '../../components/modal/modal';
import { ClassServiceProvider } from '../../providers/class-service';
import { Helper } from '../../providers/helper';
// import { Storage } from '@ionic/storage';
import { MenuController } from 'ionic-angular/components/app/menu-controller';
//import { CONSTANTS } from '../../config/constants';
// import { AuthServiceProvider } from '../../providers/auth-service';
import { DataServiceProvider } from '../../providers/data.service';

@Component({
	selector: 'meditation',
	templateUrl: 'meditation.html'
})
export class MeditationComponent implements OnInit,OnDestroy {

  	title: string = 'Practice';
	isRunning: boolean = false;
	meditationTimerCtrl: any;
	meditationId:number = null;
	interval: any;
	practiceFileList:any;
	indexValue: any;
	practiceFileLength = 0
	btnIndex = 0;
	audioSrc:any;
	loading: any;
	shownGroup = null;
	result: any;
	classId: any = 0;

	constructor(public viewCtrl: ViewController, public loadCtrl: LoadingController, public navCtrl: NavController, public navParams: NavParams, public menu: MenuController, public helper: Helper,   private dataService: DataServiceProvider,public classService: ClassServiceProvider) {
		this.menu.enable(true);
		
		this.dataService.currentClass.subscribe(classId => {
			this.classId = classId;
		});
	}

	dismiss() {
		this.stop(false);
		this.viewCtrl.dismiss();
	}

	ngOnInit() {

		
		this.btnIndex = 0;
		this.meditationTimerCtrl = new MeditationTimerViewController(this,(param)=>{
			 // callback when completing the timer
			let data = {
				prepare_time: this.meditationTimerCtrl.meditationTimer.prepareTime,
				interval_time: this.meditationTimerCtrl.meditationTimer.intervalTime,
				meditation_time: this.meditationTimerCtrl.meditationTimer.meditationTime,
				current_meditation_time: this.meditationTimerCtrl.meditationTimer.currentMeditationTime,
				meditation_id: this.meditationId,
			}
			this.classService.updateTime(data).then(res => {
				
				if (res['status'] == 'success') {
					clearInterval(this.interval);
				}
			});

		});

		this.classService.course().then(res=>{
			let data:any=res['data'];
			this.meditationTimerCtrl.onLoadDisplay();
			if(data && data.hasOwnProperty('bell_audio') && data['bell_audio'].hasOwnProperty('bell_unique_name')){
				this.audioSrc = data.audio_url+data['bell_audio']['bell_unique_name'];
				this.meditationTimerCtrl.loadSounds();
			}
		});

		clearInterval(this.interval);

		this.interval = setInterval(() => {
			if(this.meditationTimerCtrl.meditationTimer.currentPhase == 2){
				let data = {
					prepare_time: this.meditationTimerCtrl.meditationTimer.prepareTime,
					interval_time: this.meditationTimerCtrl.meditationTimer.intervalTime,
					meditation_time: this.meditationTimerCtrl.meditationTimer.meditationTime,
					current_meditation_time: this.meditationTimerCtrl.meditationTimer.currentMeditationTime,
					meditation_id: this.meditationId,
				}
				this.classService.updateTime(data).then(res => {
					
					if (res['status'] == 'success') {
						this.meditationId = res['id'];
					}
				});
			}
		}, 10000);

		

	}

	ngOnDestroy(){
		// this.stop(false);
		clearInterval(this.interval);
	}


	getStepsTime(timeType) {
		return this.meditationTimerCtrl.getStepsTime(timeType);
	}

	start($event){
		if(this.meditationTimerCtrl.meditationTimer.prepareTime <= 1){
			this.helper.presentToast('Preparation time should be greater than 1.', 'error');	
		}
		this.meditationId = null;
		let data = {
			prepare_time: this.meditationTimerCtrl.meditationTimer.prepareTime,
			interval_time: this.meditationTimerCtrl.meditationTimer.intervalTime,
			meditation_time: this.meditationTimerCtrl.meditationTimer.meditationTime,
			current_meditation_time:0,
			meditation_id:this.meditationId,
		}
		this.classService.updateTime(data).then(res => {
			
			if (res['status'] == 'success') {
				this.meditationId = res['id'];
			}
		});
		this.meditationTimerCtrl.startButtonPushedAction($event.target);
	}
	testSound(){
		this.meditationTimerCtrl.testSound();
	}


	settings(index){
		this.btnIndex = index;
		return this.meditationTimerCtrl.settingsButtonPushedAction(index);
	}
	pause(target){
		return this.meditationTimerCtrl.pauseButtonPushedAction(target);
	}
	stop(target){
		let data = {
			prepare_time: this.meditationTimerCtrl.meditationTimer.prepareTime,
			interval_time: this.meditationTimerCtrl.meditationTimer.intervalTime,
			meditation_time: this.meditationTimerCtrl.meditationTimer.meditationTime,
			current_meditation_time: this.meditationTimerCtrl.meditationTimer.currentMeditationTime,
			meditation_id: this.meditationId,
		}
		this.classService.updateTime(data).then(res => {
			
			if (res['status'] == 'success') {
				this.meditationId = null;
				clearInterval(this.interval);
			}
		});
		return this.meditationTimerCtrl.stopButtonPushedAction(target);
	}

	timeArrowMinUpClick(){
		return false;
	}
	timeArrowMinUpMouseDown(){
		return this.meditationTimerCtrl.plusButtonPushedAction(60);
	}
	timeArrowMinUpMouseUp(){
		return this.meditationTimerCtrl.plusButtonReleasedAction();
	}
	timeArrowMinUpMouseOut(){
		return this.meditationTimerCtrl.plusButtonReleasedAction();
	}

	timeArrowMinDownClick(){
		return false;
	}
	timeArrowMinDownMouseDown(){
		return this.meditationTimerCtrl.plusButtonPushedAction(-60);
	}
	timeArrowMinDownMouseUp(){
		return this.meditationTimerCtrl.plusButtonReleasedAction();
	}
	timeArrowMinDownMouseOut(){
		return this.meditationTimerCtrl.plusButtonReleasedAction();
	}

	timeArrowSecUpClick(){
		return false;
	}
	timeArrowSecUpMouseDown(){
		return this.meditationTimerCtrl.plusButtonPushedAction(1);
	}
	timeArrowSecUpMouseUp(){
		return this.meditationTimerCtrl.plusButtonReleasedAction();
	}
	timeArrowSecUpMouseOut(){
		return this.meditationTimerCtrl.plusButtonReleasedAction();
	}

	timeArrowSecDownClick(){
		return false;
	}
	timeArrowSecDownMouseDown(){
		return this.meditationTimerCtrl.plusButtonPushedAction(-1);
	}
	timeArrowSecDownMouseUp(){
		return this.meditationTimerCtrl.plusButtonReleasedAction();
	}
	timeArrowSecDownMouseOut(){
		return this.meditationTimerCtrl.plusButtonReleasedAction();
	}


}
class Timer{
	t:any;
	isRunning=false;
	constructor(public target,public action,public interval,public repeat, public timesUpCallback){
		
	}

	fire () {
		this.target[this.action].call(this.target);
		if (this.repeat === true) {
			this.cycle();
		}
	}

	cycle () {
		if (this.isRunning) {
			let self = this;
			this.t = window.setTimeout(function () { self.fire(); }, this.interval);
		}
	}

	start () {
		this.isRunning = true;
		this.fire();
	}

	stop () {		
		this.isRunning = false;
		window.clearTimeout(this.t);
		if(typeof this.timesUpCallback == 'function'){
			this.timesUpCallback();
		}
	}
};

class MeditationTimer {
	defaults = {
		prepareTime: 10,
		meditationTime: 60,
		intervalTime: 10
	};
	phases = {
		start: 0,
		prepare: 1,
		meditation: 2,
		end: 3
	};
	timer:any;
	currentPhase = this.phases.start;
	currentMeditationTime = 0;
	currentIntervalTime = 0;
	prepareTime = 0;
	meditationTime = 0;
	intervalTime = 0;
	totalIntervals = 0;
	isRunning=false;
	currentInterval = 1;
	delegate:any;
	constructor(public delegateRef, public mTimer, callback){
		this.timer = new Timer(this, "fireTimer", 1000, true, callback);
		this.delegate = delegateRef;

	}
	saveTimer (){
		
	};

	loadTimer () {
		
		this.prepareTime = this.defaults.prepareTime;
		this.meditationTime = this.defaults.meditationTime;
		this.intervalTime = this.defaults.intervalTime;
		//}
	};

	start() {
		if (this.isRunning) {
			return;
		}
		if (this.currentPhase == this.phases.start) {
			if (this.intervalTime > 0) {
				this.totalIntervals = Math.ceil(this.meditationTime / this.intervalTime);
			}
			this.currentPhase = this.calculateNextPhase(this.currentPhase);
		}
		if (this.currentPhase != this.phases.end) {
			this.isRunning = true;
			this.mTimer.isRunning = true;
			this.timer.start();
		}
		else {
			this.endSession();
		}

		//this.classService.updateTime().then({})
	};

	stop() {
		if (this.isRunning) {
			this.isRunning = false;
			this.timer.stop();
		}
	};

	reset() {
		this.stop();
		this.currentPhase = this.phases.start;
		this.currentMeditationTime = 0;
		this.currentIntervalTime = 0;
		this.totalIntervals = 0;
		this.currentInterval = 1;

	};

	endSession() {
		this.reset();
		this.mTimer.isRunning=false;
		this.delegate.sessionEnded(this);//['sessionEnded'].call(this);
		//this.meditationTimer.sessionEnded(this);
	}

	calculateNextPhase(phase) {
		switch (phase) {
			case this.phases.start:
				if (this.prepareTime > 0) {
					this.currentPhase = this.phases.prepare;
				}
				else if (this.meditationTime > 0) {
					this.currentPhase = this.phases.meditation;
				}
				else {
					this.currentPhase = this.phases.end;
				}
				break;
			case this.phases.prepare:
				if (this.meditationTime > 0) {
					this.currentPhase = this.phases.meditation;
				}
				else {
					this.currentPhase = this.phases.end;
				}
				break;
			case this.phases.meditation:
				this.currentPhase = this.phases.end;
				break;
			default:
				break;
		}

		this.delegate.phaseChanged();// ['phaseChanged'].call(this);

		//this.meditationTimer.phaseChanged(this);
		if (this.currentPhase == this.phases.prepare) {
			this.currentMeditationTime = this.prepareTime;
		}

		if (this.currentPhase == this.phases.meditation) {
			this.currentMeditationTime = this.meditationTime;
			if (this.intervalTime > 0) {
				this.currentIntervalTime = this.intervalTime;
			}
		}

		return this.currentPhase;
	};

	fireTimer() {
		this.currentMeditationTime = (this.currentMeditationTime - 1);

		if (this.currentPhase == this.phases.meditation && this.intervalTime > 0) {
			this.currentIntervalTime = (this.currentIntervalTime - 1);
			if (this.currentIntervalTime == 0 && this.currentMeditationTime != 0) {
				this.currentIntervalTime = this.intervalTime;
				this.currentInterval++;
				this.delegate.intervalEnded(this);
			}
			this.delegate.intervalFired(this);
		}

		this.delegate.timerFired(this);

		if (this.currentMeditationTime == 0) {
			if (this.calculateNextPhase(this.currentPhase) == this.phases.end) {
				this.delegate.sessionCompleted(this);
				this.endSession();
			}
		}
	}

};


class SoundEngine {
	chimes = {};
	playPromise={};
	constructor(){

	}
	loadSounds (mTimer) {
		for (let i = 0; i < 4; i++) {
			// let url = "";

			this.chimes[i] = document.querySelector('#audioChime_'+i);

			// if (this.chimes[i].canPlayType("audio/ogg")) {
			// 	url = "assets/media/chime.ogg";
			// } else if (this.chimes[i].canPlayType("audio/mpeg")) {
			// 	url = "assets/media/chime.mp3";
			// }
			this.chimes[i].src = mTimer.audioSrc;
			// this.chimes[i].src = url;
			this.chimes[i].loop = false;
			this.chimes[i].autoplay = false;
			this.chimes[i].load();
		}
	};

	playSound (i) {
		if (typeof this.chimes[i] !== 'undefined') {
			if (!this.chimes[i].paused) {
				this.chimes[i].pause();
				this.chimes[i].currentTime = 0;
			}
		}

		this.playPromise[i]=this.chimes[i].play();
	}

	stopSound () {
		for (var i = 0; i < 4; i++) {
			if (typeof this.chimes[i] !== 'undefined') {
				if (!this.chimes[i].paused) {
					this.chimes[i].pause();
					this.chimes[i].currentTime = 0;
				}
			}
		}
	}
};

class MeditationTimerViewController {
	soundEngine:any;
	meditationTimer:any;
	settings = {
		prepare: 0,
		meditation: 1,
		interval: 2
	};
	activeSetting = this.settings.prepare;
	chime;
	chimeTimer;
	numberOfChimes = 0;
	totalChimes = 0;

	settingTimer;
	settingTimerIntervals = 300;

	constructor(public mTimer,callback){
		this.meditationTimer = new MeditationTimer(this,mTimer,callback);
		this.soundEngine= new SoundEngine();
	}
	loadSounds() {
		this.soundEngine.loadSounds(this.mTimer);
	}

	// SOUNDS
	playChime (i) {
		this.soundEngine.playSound(i);

		i++;
		this.numberOfChimes--;

		let chimeDuration;

		if (this.totalChimes == 2 && this.numberOfChimes == 1) {
			chimeDuration = 6000;
		}

		if (this.totalChimes == 3 && this.numberOfChimes == 2) {
			chimeDuration = 4000;
		}

		if (this.totalChimes == 3 && this.numberOfChimes == 1) {
			chimeDuration = 10000;
		}

		if (this.numberOfChimes > 0) {
			let self = this;
			this.chimeTimer = window.setTimeout(function () {
				self.playChime(i);
			}, chimeDuration);
		}
	};

	chimeOne () {
		this.numberOfChimes = 1;
		this.totalChimes = 1;
		window.clearTimeout(this.chimeTimer);
		this.playChime(1);
	};

	chimeTwo () {
		this.numberOfChimes = 1; // change to 1
		this.totalChimes = 1; // change to 1
		this.playChime(1);
	};

	chimeThree () {
		this.numberOfChimes = 3;
		this.totalChimes = 3;
		this.playChime(1);
	};

	// HELPERS
	convertToTimeFormat(num) {
		let hrs = Math.floor(num / 3600);
		let mins = Math.floor((num % 3600) / 60);
		let secs = (num % 60);
		return ((hrs > 0) ? hrs + ":" : "") + ((mins < 10) ? "0" : "") + mins + ":" + ((secs < 10) ? "0" : "") + secs;
	};

	getMinutesFormat(num) {
		let mins = Math.floor((num % 3600) / 60);
		return ((mins < 10) ? "0" : "") + mins;
	};

	getSecondsFormat(num) {
		let secs = (num % 60);
		return ((secs < 10) ? "0" : "") + secs;
	};

	getStepsTime(time){
		return this.convertToTimeFormat(this.meditationTimer[time]);
	}

	changeSettingValue(i) {
		if (this.activeSetting == this.settings.prepare) {
			this.meditationTimer.prepareTime += i;
			this.meditationTimer.prepareTime = Math.min(Math.max(this.meditationTimer.prepareTime,0),3599);
			let m =this.getMinutesFormat(this.meditationTimer.prepareTime);
			document.querySelector("#overAllMinutes").innerHTML = m;
			document.querySelector("#overAllSeconds").innerHTML = (this.getSecondsFormat(this.meditationTimer.prepareTime));

			document.querySelector("#mtPrepareTimeSetting").innerHTML = (this.convertToTimeFormat(this.meditationTimer.prepareTime));
		}
		else if (this.activeSetting == this.settings.meditation) {
			this.meditationTimer.meditationTime += i;
			this.meditationTimer.meditationTime = Math.min(Math.max(this.meditationTimer.meditationTime,0),3599);
			document.querySelector("#overAllMinutes").innerHTML = (this.getMinutesFormat(this.meditationTimer.meditationTime));
			document.querySelector("#overAllSeconds").innerHTML = (this.getSecondsFormat(this.meditationTimer.meditationTime));
			document.querySelector("#mtMeditationTimeSetting").innerHTML = (this.convertToTimeFormat(this.meditationTimer.meditationTime));
		}
		else if (this.activeSetting == this.settings.interval) {
			this.meditationTimer.intervalTime += i;
			this.meditationTimer.intervalTime = Math.min(Math.max(this.meditationTimer.intervalTime,0),3599);
			document.querySelector("#overAllMinutes").innerHTML = (this.getMinutesFormat(this.meditationTimer.intervalTime));
			document.querySelector("#overAllSeconds").innerHTML = (this.getSecondsFormat(this.meditationTimer.intervalTime));
			document.querySelector("#mtIntervalTimeSetting").innerHTML = (this.convertToTimeFormat(this.meditationTimer.intervalTime));
		}
		let self = this;
		this.settingTimer = window.setTimeout(function () { self.changeSettingValue(i); }, this.settingTimerIntervals)
		this.settingTimerIntervals = (this.settingTimerIntervals > 100) ? (this.settingTimerIntervals - 50) : this.settingTimerIntervals;
	};

	invalidateChangeSettingValue() {
		window.clearTimeout(this.settingTimer);
		this.settingTimerIntervals = 300;
	};


	// DISPLAY

	displayForSetup(setting) {
		switch (setting) {
			case this.settings.prepare:
				this.activeSetting = this.settings.prepare;
				//document.querySelector("#mtOverallTime").innerHTML = (this.convertToTimeFormat(this.meditationTimer.prepareTime));
				
				document.querySelector("#overAllMinutes").innerHTML = (this.getMinutesFormat(this.meditationTimer.prepareTime));
				document.querySelector("#overAllSeconds").innerHTML = (this.getSecondsFormat(this.meditationTimer.prepareTime));
				break;
			case this.settings.meditation:
				this.activeSetting = this.settings.meditation;
				//document.querySelector("#mtOverallTime").innerHTML = (this.convertToTimeFormat(this.meditationTimer.meditationTime));
				document.querySelector("#overAllMinutes").innerHTML = (this.getMinutesFormat(this.meditationTimer.meditationTime));
				document.querySelector("#overAllSeconds").innerHTML = (this.getSecondsFormat(this.meditationTimer.meditationTime));
				break;
			case this.settings.interval:
				this.activeSetting = this.settings.interval;
				//document.querySelector("#mtOverallTime").innerHTML = (this.convertToTimeFormat(this.meditationTimer.intervalTime));
				document.querySelector("#overAllMinutes").innerHTML = (this.getMinutesFormat(this.meditationTimer.intervalTime));
				document.querySelector("#overAllSeconds").innerHTML = (this.getSecondsFormat(this.meditationTimer.intervalTime));
				break;
			default:
				break;
		}

		let $activeElem = document.querySelectorAll("#mtSettings button");
		for(let i=0,l=$activeElem.length;i<l;i++){
			$activeElem[i].classList.add('btn-white');
		}
		document.querySelectorAll("#mtSettings ion-col") &&
			document.querySelectorAll("#mtSettings ion-col").length &&
			document.querySelectorAll("#mtSettings ion-col")[setting].querySelector('button').classList.remove("btn-white");
	};

	displayForPhase(phase) {
		switch (phase) {
			case this.meditationTimer.phases.start:
				//document.querySelector("#mtOverallTime").innerHTML = (this.convertToTimeFormat(this.meditationTimer.meditationTime));
				document.querySelector("#overAllMinutes").innerHTML = (this.getMinutesFormat(this.meditationTimer.meditationTime));
				document.querySelector("#overAllSeconds").innerHTML = (this.getSecondsFormat(this.meditationTimer.meditationTime));

				//document.querySelector("#mtIntervalTime").innerHTML = ("");
				break;
			case this.meditationTimer.phases.prepare:
				//document.querySelector("#mtOverallTime").innerHTML = (this.convertToTimeFormat(this.meditationTimer.prepareTime));
				document.querySelector("#overAllMinutes").innerHTML = (this.getMinutesFormat(this.meditationTimer.prepareTime));
				document.querySelector("#overAllSeconds").innerHTML = (this.getSecondsFormat(this.meditationTimer.prepareTime));
				//document.querySelector("#mtIntervalTime").innerHTML = ("");
				break;
			case this.meditationTimer.phases.meditation:
				this.chimeOne();
				//document.querySelector("#mtOverallTime").innerHTML = (this.convertToTimeFormat(this.meditationTimer.meditationTime));
				document.querySelector("#overAllMinutes").innerHTML = (this.getMinutesFormat(this.meditationTimer.meditationTime));
				document.querySelector("#overAllSeconds").innerHTML = (this.getSecondsFormat(this.meditationTimer.meditationTime));
				if (this.meditationTimer.intervalTime > 0) {
					document.querySelector("#mtIntervalTime").innerHTML = (this.convertToTimeFormat(this.meditationTimer.intervalTime) + " (" + this.meditationTimer.currentInterval + "/" + this.meditationTimer.totalIntervals + ")");
				} else {
					document.querySelector("#mtIntervalTime").innerHTML = ("");
				}
				break;
			default:
				break;
		}
	};

	onLoadDisplay() {
		this.meditationTimer.loadTimer();
		this.displayForSetup(this.activeSetting);
		document.querySelector("#mtPrepareTimeSetting").innerHTML = (this.convertToTimeFormat(this.meditationTimer.prepareTime));
		document.querySelector("#mtMeditationTimeSetting").innerHTML = (this.convertToTimeFormat(this.meditationTimer.meditationTime));
		document.querySelector("#mtIntervalTimeSetting").innerHTML = (this.convertToTimeFormat(this.meditationTimer.intervalTime));
		//(document.querySelector("#mtIntervalTime") as HTMLElement).style.display="none";
		// (document.querySelector("#mtStartButton") as HTMLElement).innerText = "Meditate";
		// (document.querySelector("#mtPauseButton") as HTMLElement).innerText = "Pause";
		// (document.querySelector("#mtStopButton") as HTMLElement).innerText = "Stop";
		// (document.querySelector("#mtPauseButton") as HTMLElement).style.display="none";
		// (document.querySelector("#mtStopButton") as HTMLElement).style.display="none";
	};

	// DELEGATE

	timerFired() {
		//document.querySelector("#mtOverallTime").innerHTML = (this.convertToTimeFormat(this.meditationTimer.currentMeditationTime));
		document.querySelector("#overAllMinutes").innerHTML = (this.getMinutesFormat(this.meditationTimer.currentMeditationTime));
		document.querySelector("#overAllSeconds").innerHTML = (this.getSecondsFormat(this.meditationTimer.currentMeditationTime));
	};

	intervalFired() {
		if(document.querySelector("#mtIntervalTime")){
			if (this.meditationTimer.currentIntervalTime > 0) {
				document.querySelector("#mtIntervalTime").innerHTML = (this.convertToTimeFormat(this.meditationTimer.currentIntervalTime) + " (" + this.meditationTimer.currentInterval + "/" + this.meditationTimer.totalIntervals + ")");
			}
			else {
				document.querySelector("#mtIntervalTime").innerHTML = (this.convertToTimeFormat(this.meditationTimer.intervalTime) + " (" + this.meditationTimer.currentInterval + "/" + this.meditationTimer.totalIntervals + ")");
			}
		}
	};

	intervalEnded() {
		this.chimeTwo();
	};

	sessionCompleted() {
		this.chimeThree();
	};

	phaseChanged() {
		this.displayForPhase(this.meditationTimer.currentPhase);
	};

	sessionEnded(m) {
		this.displayForPhase(this.meditationTimer.currentPhase);
		this.displayForSetup(this.activeSetting);
	};

	// ACTIONS

	settingsButtonPushedAction(index) {
		if (!this.meditationTimer.isRunning) {
			this.displayForSetup(index);
		}
		return false;
	};

	pauseButtonPushedAction(sender) {
		if (this.meditationTimer.isRunning) {
			this.meditationTimer.stop();
			sender.innerText = "Resume";
		}
		else {
			this.meditationTimer.start();
			sender.innerText = "Pause";
		}
		return false;
	};

	startButtonPushedAction(sender) {
		if (!this.meditationTimer.isRunning) {
			this.meditationTimer.start();
			this.meditationTimer.saveTimer();
			this.soundEngine.stopSound();
		}
		return false;
	};

	stopButtonPushedAction(sender) {

		this.meditationTimer.endSession();
		this.soundEngine.stopSound();
		return false;
	};

	plusButtonPushedAction(secs) {
		this.changeSettingValue(secs);
		return false;
	};

	minusButtonPushedAction(secs) {
		this.changeSettingValue(secs);
		return false;
	};

	plusButtonReleasedAction() {
		this.invalidateChangeSettingValue();
		return false;
	};

	minusButtonReleasedAction() {
		this.invalidateChangeSettingValue();
		return false;
	};

	testSound() {
		this.soundEngine.stopSound();
		this.soundEngine.playSound(0);
		return false;
	};
};
