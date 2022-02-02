import { ErrorHandler, NgModule } from '@angular/core';
import { HttpModule } from '@angular/http';
import { BrowserModule } from '@angular/platform-browser';
import { SplashScreen } from '@ionic-native/splash-screen';
import { StatusBar } from '@ionic-native/status-bar';
import { IonicStorageModule } from '@ionic/storage';
import { NgIdleModule } from '@ng-idle/core';
//import { NgIdleKeepaliveModule } from '@ng-idle/keepalive'; // this includes the core NgIdleModule but includes keepalive providers for easy wireup
import { MomentModule } from 'angular2-moment';
// Social login
// import { AuthServiceConfig, FacebookLoginProvider, GoogleLoginProvider, SocialLoginModule } from 'angular4-social-login';
import { IonicApp, IonicErrorHandler, IonicModule, IonicPageModule } from 'ionic-angular';
import { InputTrimModule } from 'ng2-trim-directive';
import { VgBufferingModule } from 'videogular2/buffering';
import { VgControlsModule } from 'videogular2/controls';
import { VgCoreModule } from 'videogular2/core';
import { VgOverlayPlayModule } from 'videogular2/overlay-play';
import { AppHeaderComponent } from '../components/app-header/app-header';
import { BackStaticHeaderComponent } from '../components/back-static-header/back-static-header';
import { BreadcrumbComponent } from '../components/breadcrumb/breadcrumb';
import { ModalComponent } from '../components/modal/modal';
import { MeditationComponent } from '../components/meditation/meditation';
import { ProgressBarComponent } from '../components/progress-bar/progress-bar';
import { StaticFooterComponent } from '../components/static-footer/static-footer';
import { AboutPage } from '../pages/about/about';
import { AcknowledgementsPage } from '../pages/acknowledgements/acknowledgements';
import { AudioPage } from '../pages/audio/audio';
import { ClassPage } from '../pages/class/class';
import { ClassSchedulePage } from '../pages/class-schedule/class-schedule';
import { CommunityDiscussionPage } from '../pages/community-discussion/community-discussion';
import { CommunityUserPage } from '../pages/community-user/community-user';
import { CommunityPage } from '../pages/community/community';
import { ContactPage } from '../pages/contact/contact';
import { DashboardPage } from '../pages/dashboard/dashboard';
import { FeedbackPage } from '../pages/feedback/feedback';
import { ForgotPasswordPage } from '../pages/forgot-password/forgot-password';
import { GeneralPage } from '../pages/general/general';
import { HomePage } from '../pages/home/home';
import { HomeworkDetailPage } from '../pages/homework-detail/homework-detail';
import { HomeworkReadingDetailPage } from '../pages/homework-reading-detail/homework-reading-detail';
import { HomeworkPage } from '../pages/homework/homework';
import { IntentionPage } from '../pages/intention/intention';
import { MeditationTimerPage } from '../pages/meditation-timer/meditation-timer';
import { NumberedGeneralPage } from '../pages/numbered-general/numbered-general';
// Import Pages
import { OverviewPage } from '../pages/overview/overview';
import { PrivacyPolicyPage } from '../pages/privacy-policy/privacy-policy';
import { ProfilePage } from '../pages/profile/profile';
import { QuestionPage } from '../pages/question/question';
import { ResetPasswordPage } from '../pages/reset-password/reset-password';
import { ReviewDetailPage } from '../pages/review-detail/review-detail';
import { ReviewPage } from '../pages/review/review'; //import { ReviewDetailPage } from '../pages/review-detail/review-detail';
import { SigninPage } from '../pages/signin/signin';
import { SignupPage } from '../pages/signup/signup';
import { TabsPage } from '../pages/tabs/tabs';
import { TermsConditionsPage } from '../pages/terms-conditions/terms-conditions';
import { TestimonialPage } from '../pages/testimonial/testimonial';
import { TopicPage } from '../pages/topic/topic';
import { VideoIntroPage } from '../pages/video-intro/video-intro';
import { VideoPage } from '../pages/video/video';
import { AuthServiceProvider } from '../providers/auth-service';
// Import Provider
import { ClassServiceProvider } from '../providers/class-service';
import { CommunityServiceProvider } from '../providers/community-service';
import { DataServiceProvider } from '../providers/data.service';
import { Helper } from '../providers/helper';
import { HomeworkServiceProvider } from '../providers/homework-service';
import { ReviewServiceProvider } from '../providers/review-service';
import { MyApp } from './app.component';
import { NotificationPage } from '../pages/notification/notification';
import { WelcomeVideoPage } from '../pages/welcome-video/welcome-video';


@NgModule({
	declarations: [
		MyApp,
		HomePage,
		SigninPage,
		SignupPage,
		TabsPage,
		DashboardPage,
		ClassPage,
		ClassSchedulePage,
		ReviewPage,
		HomeworkPage,
		MeditationTimerPage,
		CommunityPage,
		ContactPage,
		FeedbackPage,
		NotificationPage,
		OverviewPage,
		AboutPage,
		AcknowledgementsPage,
		PrivacyPolicyPage,
		TermsConditionsPage,
		ProfilePage,
		ResetPasswordPage,
		ForgotPasswordPage,
		GeneralPage,
		AudioPage,
		VideoIntroPage,
		VideoPage,
		TestimonialPage,
		NumberedGeneralPage,
		TopicPage,
		QuestionPage,
		IntentionPage,
		CommunityDiscussionPage,
		CommunityUserPage,
		ReviewDetailPage,
		HomeworkDetailPage,
		BackStaticHeaderComponent,
		ModalComponent,
		MeditationComponent,
		AppHeaderComponent,
		ProgressBarComponent,
		BreadcrumbComponent,
		StaticFooterComponent,
		HomeworkReadingDetailPage,
		WelcomeVideoPage
	],
	imports: [
		BrowserModule,
		HttpModule,
		MomentModule,
		//NgIdleKeepaliveModule.forRoot(),
		NgIdleModule.forRoot(),
		IonicModule.forRoot(
			MyApp,
			{
				backButtonText: '',
			},
			{
				links: [
					// Root for dashboard
					{ component: DashboardPage, name: 'DashboardPage', segment: '' },
					{ component: WelcomeVideoPage, name: 'WelcomeVideoPage', segment: '' },
					// Root for classes
					{ component: ClassSchedulePage, name: 'ClassSchedulePage', segment: '' },
					{ component: ClassPage, name: 'ClassPage', segment: '' },
					{ component: GeneralPage, name: 'GeneralPage', segment: '' },
					{ component: AudioPage, name: 'AudioPage', segment: '' },
					{ component: VideoIntroPage, name: 'VideoIntroPage', segment: '' },
					{ component: VideoPage, name: 'VideoPage', segment: '' },
					{ component: TestimonialPage, name: 'TestimonialPage', segment: '' },
					{ component: NumberedGeneralPage, name: 'NumberedGeneralPage', segment: '' },
					{ component: TopicPage, name: 'TopicPage', segment: '' },
					{ component: QuestionPage, name: 'QuestionPage', segment: '' },
					{ component: IntentionPage, name: 'IntentionPage', segment: '' },
					// Root for review
					{ component: ReviewPage, name: 'ReviewPage', segment: '' },
					{ component: ReviewDetailPage, name: 'ReviewDetailPage', segment: '' },
					// Root for homework
					{ component: HomeworkPage, name: 'HomeworkPage', segment: '' },
					{ component: MeditationTimerPage, name: 'MeditationTimerPage', segment: '' },
					{ component: HomeworkDetailPage, name: 'HomeworkDetailPage', segment: '' },
					// Root for community
					{ component: CommunityPage, name: 'CommunityPage', segment: '' },
					{ component: CommunityUserPage, name: 'CommunityUserPage', segment: '' },

					// Root for Other pages
					{ component: SignupPage, name: 'SignupPage', segment: 'sign-up/:accesscode' },
					{ component: HomePage, name: 'HomePage', segment: 'home' },
					{ component: SigninPage, name: 'SigninPage', segment: 'sign-in/:course' },
					{ component: SigninPage, name: 'SigninPage', segment: 'sign-in' },
					{ component: ForgotPasswordPage, name: 'ForgotPasswordPage', segment: 'forgot-password' },
					//{ component: HomePage, name: 'HomePage', segment:''},
					{ component: AboutPage, name: 'AboutPage', segment: 'about-us' },
					{ component: AcknowledgementsPage, name: 'AcknowledgementsPage', segment: 'acknowledgements' },
					{ component: PrivacyPolicyPage, name: 'PrivacyPolicyPage', segment: 'privacy-policy' },
					{ component: TermsConditionsPage, name: 'TermsConditionsPage', segment: 'terms-conditions' },
					{ component: ContactPage, name: 'ContactPage', segment: 'contact' },
					{ component: NotificationPage, name: 'NotificationPage', segment: 'notification' },
					{ component: CommunityDiscussionPage, name: 'CommunityDiscussionPage', segment: '' },
					{ component: FeedbackPage, name: 'FeedbackPage', segment: 'feedback' },
					{ component: OverviewPage, name: 'OverviewPage', segment: 'faqs' },
					{ component: ProfilePage, name: 'ProfilePage', segment: 'profile' },
				],
			}
		),

		IonicPageModule.forChild(GeneralPage),

		//IonicPageModule.forChild(AudioPage),
		IonicStorageModule.forRoot(),
		//AudioPageModule,
		//DashboardPageModule,
		//GeneralPageModule,
		/*QuestionPageModule,
		VideoIntroPageModule,
		VideoPageModule,
		TopicPageModule,
		TestimonialPageModule,
		IntentionPageModule,
		ReviewDetailPageModule,
		HomeworkDetailPageModule,
		CommonModule,
		ForgotPasswordPageModule,
		ResetPasswordPageModule,
		CommunityDiscussionPageModule,
		CommunityUserPageModule,*/
		//ComponentsModule,
		//ProgressBarComponent,
		// SocialLoginModule.initialize(config),

		VgCoreModule,
		VgControlsModule,
		VgOverlayPlayModule,
		VgBufferingModule,
		InputTrimModule
	],
	bootstrap: [IonicApp],
	entryComponents: [
		WelcomeVideoPage,
		MyApp,
		HomePage,
		SigninPage,
		SignupPage,
		DashboardPage,
		TabsPage,
		ClassPage,
		ClassSchedulePage,
		ReviewPage,
		HomeworkPage,
		MeditationTimerPage,
		CommunityPage,
		ContactPage,
		AboutPage,
		AcknowledgementsPage,
		PrivacyPolicyPage,
		TermsConditionsPage,
		FeedbackPage,
		NotificationPage,
		OverviewPage,
		ProfilePage,
		ResetPasswordPage,
		ForgotPasswordPage,
		GeneralPage,
		NumberedGeneralPage,
		AudioPage,
		VideoIntroPage,
		VideoPage,
		TestimonialPage,
		TopicPage,
		QuestionPage,
		IntentionPage,
		CommunityDiscussionPage,
		CommunityUserPage,
		ReviewDetailPage,
		HomeworkDetailPage,

		ModalComponent,
		MeditationComponent,
		AppHeaderComponent,
		ProgressBarComponent,
		BreadcrumbComponent,
		StaticFooterComponent,
		HomeworkReadingDetailPage,
	],
	providers: [
		StatusBar,
		SplashScreen,
		AuthServiceProvider,
		{ provide: ErrorHandler, useClass: IonicErrorHandler },
		Helper,
		ClassServiceProvider,
		DataServiceProvider,
		ReviewServiceProvider,
		HomeworkServiceProvider,
		CommunityServiceProvider,

	],
	exports: [ProgressBarComponent],
})
export class AppModule { }
