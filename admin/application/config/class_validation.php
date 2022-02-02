<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$CI = & get_instance();

$config = array(
    'general' => array(
        array(
            'field' => 'title',
            'label' => 'title',
            'rules' => 'required'
        ),
        array(
            'field' => 'header',
            'label' => 'header',
            'rules' => 'required'
        ),
        array(
            'field' => 'content',
            'label' => 'content',
            'rules' => 'required'
        ),
        array(
            'field' => 'button_text',
            'label' => 'button text',
            'rules' => 'required'
        ),
    ),
    'audio' => array(
        array(
            'field' => 'title',
            'label' => 'title',
            'rules' => 'required'
        ),
        array(
            'field' => 'script',
            'label' => 'script',
            'rules' => 'required'
        ),
        array(
            'field' => 'audio_text',
            'label' => 'audio text',
            'rules' => 'required'
        ),
        array(
            'field' => 'button_text',
            'label' => 'button text',
            'rules' => 'required'
        ),
        array(
            'field' => 'practice_type',
            'rules' => 'required'
        ),
    ),
    'video' => array(
        array(
            'field' => 'title',
            'label' => 'title',
            'rules' => 'required'
        ),
        array(
            'field' => 'header',
            'label' => 'header',
            'rules' => 'required'
        ),
        array(
            'field' => 'pretext',
            'label' => 'pretext',
            'rules' => 'required'
        ),
        array(
            'field' => 'script',
            'label' => 'script',
            'rules' => 'required'
        ),
        array(
            'field' => 'post_text',
            'label' => 'post text',
            'rules' => 'required'
        ),
        array(
            'field' => 'button_text',
            'label' => 'button text',
            'rules' => 'required'
        ),
        array(
            'field' => 'practice_type',
            'rules' => 'required'
        ),
    ),
    'question' => array(
        array(
            'field' => 'title',
            'label' => 'title',
            'rules' => 'required'
        ),
        array(
            'field' => 'question_number',
            'label' => 'question number',
            'rules' => 'required|numeric'
        ),
        array(
            'field' => 'question_color',
            'label' => 'question color',
            'rules' => 'required'
        ),
        array(
            'field' => 'question_text',
            'label' => 'question text',
            'rules' => 'required'
        ),
        array(
            'field' => 'button_text',
            'label' => 'button text',
            'rules' => 'required'
        ),
    ),
    'topic' => array(
        array(
            'field' => 'title',
            'label' => 'title',
            'rules' => 'required'
        ),
        array(
            'field' => 'intro_text',
            'label' => 'intro text',
            'rules' => 'required'
        ),
        array(
            'field' => 'button_text',
            'label' => 'button text',
            'rules' => 'required'
        ),
        array(
            'field' => 'topic_title[]',
            'label' => 'topic title',
            'rules' => 'required'
        ),
        array(
            'field' => 'topic_text[]',
            'label' => 'topic text',
            'rules' => 'required'
        ),
        array(
            'field' => 'topic_color[]',
            'label' => 'topic color',
            'rules' => 'required'
        ),
    ),
    'testimonial' => array(
        array(
            'field' => 'title',
            'label' => 'title',
            'rules' => 'required'
        ),
        array(
            'field' => 'header',
            'label' => 'header',
            'rules' => 'required'
        ),
        array(
            'field' => 'button_text',
            'label' => 'button text',
            'rules' => 'required'
        ),
//        array(
//            'field' => 'photo[]',
//            'label' => 'photo',
//            'rules' => 'required'
//        ),
        array(
            'field' => 'quote[]',
            'label' => 'quote',
            'rules' => 'required'
        ),
        array(
            'field' => 'name[]',
            'label' => 'name',
            'rules' => 'required'
        ),
    ),
    'intention' => array(
        array(
            'field' => 'title',
            'label' => 'title',
            'rules' => 'required'
        ),
        array(
            'field' => 'header',
            'label' => 'header',
            'rules' => 'required'
        ),
        array(
            'field' => 'intro_text',
            'label' => 'intro text',
            'rules' => 'required'
        ),
        array(
            'field' => 'button_text',
            'label' => 'button text',
            'rules' => 'required'
        ),
    ),
    'review' => array(
        array(
            'field' => 'title',
            'label' => 'title',
            'rules' => 'required'
        ),
        array(
            'field' => 'intro_text',
            'label' => 'intro text',
            'rules' => 'required'
        ),
//        array(
//            'field' => 'button_text',
//            'label' => 'button text',
//            'rules' => 'required'
//        ),
        array(
            'field' => 'pretext[]',
            'label' => 'pretext',
            'rules' => 'required'
        ),
	),
	'homeworkExercise' => array(
        array(
            'field' => 'title',
            'label' => 'title',
            'rules' => 'required'
        ),
        array(
            'field' => 'intro_text',
            'label' => 'intro text',
            'rules' => 'required'
        )
    ),
    'podcast' => array(
        array(
            'field' => 'title',
            'label' => 'title',
            'rules' => 'required'
        ),
        array(
            'field' => 'audio_text',
            'label' => 'audio text',
            'rules' => 'required'
        ),
        array(
            'field' => 'script',
            'label' => 'script',
            'rules' => 'required'
        ),
        array(
            'field' => 'button_text',
            'label' => 'button text',
            'rules' => 'required'
        ),
        array(
            'field' => 'practice_type',
            'rules' => 'required'
        ),
    ),
    'reading' => array(
        array(
            'field' => 'title',
            'label' => 'title',
            'rules' => 'required'
        ),
        array(
            'field' => 'intro_text',
            'label' => 'intro text',
            'rules' => 'required'
        ),
        array(
            'field' => 'reading_title[]',
            'label' => 'title',
            'rules' => 'required'
        ),
        array(
            'field' => 'reading_author[]',
            'label' => 'author',
            'rules' => 'required'
        ),
        array(
            'field' => 'reading_detail[]',
            'label' => 'link',
            'rules' => 'required|trim'
        ),
    ),
    'reflectionAnswerForm' => array(
        array(
            'field' => 'answer',
            'label' => 'Answer',
            'rules' => 'required'
        ),
        array(
            'field' => 'question_id',
            'label' => 'Question id',
            'rules' => 'required'
        )
    ),
    'intentionAnswerForm' => array(
        array(
            'field' => 'intention',
            'label' => 'Intention',
            'rules' => 'required'
        ),
        array(
            'field' => 'intention_id',
            'label' => 'Intention id',
            'rules' => 'required'
        )
    ),
    'trackingForm' => array(
        array(
            'field' => 'current_time',
            'label' => 'current time',
            'rules' => 'required'
        ),
        array(
            'field' => 'left_time',
            'label' => 'left time',
            'rules' => 'required'
        ),
        array(
            'field' => 'total_time',
            'label' => 'total time',
            'rules' => 'required'
        ),
        array(
            'field' => 'files_id',
            'label' => 'file',
            'rules' => 'required'
        ),
        array(
            'field' => 'user_page_activity_id',
            'label' => 'activity_id',
            'rules' => 'required'
        )
    ),
    
    'trackingReviewForm' => array(
        array(
            'field' => 'current_time',
            'label' => 'current time',
            'rules' => 'required'
        ),
        array(
            'field' => 'left_time',
            'label' => 'left time',
            'rules' => 'required'
        ),
        array(
            'field' => 'total_time',
            'label' => 'total time',
            'rules' => 'required'
        ),
        array(
            'field' => 'reviews_files_id',
            'label' => 'reviews has files id',
            'rules' => 'required'
        ),
      
        array(
            'field' => 'classes_id',
            'label' => 'classes id',
            'rules' => 'required'
        )
    ),
    
    'trackingExerciseForm' => array(
        array(
            'field' => 'current_time',
            'label' => 'current time',
            'rules' => 'required'
        ),
        array(
            'field' => 'left_time',
            'label' => 'left time',
            'rules' => 'required'
        ),
        array(
            'field' => 'total_time',
            'label' => 'total time',
            'rules' => 'required'
        ),
//        array(
//            'field' => 'exercises_files_id',
//            'label' => 'exercises has files id',
//            'rules' => 'required'
//        )
    ),
    'feedbackForm' => array(
        array(
            'field' => 'answer',
            'label' => 'answer',
            'rules' => 'required'
        ),
        array(
            'field' => 'question_id',
            'label' => 'question id',
            'rules' => 'required'
        ),
        array(
            'field' => 'courses_id',
            'label' => 'courses id',
            'rules' => 'required'
        ),
    ),
    
    'replyForm' => array(
        array(
            'field' => 'comment',
            'label' => 'comment',
            'rules' => 'required'
        ),
        array(
            'field' => 'answer_id',
            'label' => 'answer id',
            'rules' => 'required'
        )
    ),
    
    'commentStatusForm' => array(
        array(
            'field' => 'status',
            'label' => 'Status',
            'rules' => 'required'
        ),
        array(
            'field' => 'answer_id',
            'label' => 'answer id',
            'rules' => 'required'
        )
    ),
    'replyStatusForm' => array(
        array(
            'field' => 'status',
            'label' => 'Status',
            'rules' => 'required'
        ),
        array(
            'field' => 'answer_comments_id',
            'label' => 'comments id',
            'rules' => 'required'
        )
    ),
);
