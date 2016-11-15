define(['jquery','underscore','backbone', 'views/CourseListView','views/GradeBookView','views/SettingsPage',
'models/CourseList','models/Course','models/CourseGradebook', 'models/Settings'
],
   /**
    * @exports GradeBookRouter
    */

function($,_,Backbone,CourseListView,GradeBookView,SettingsPage, CourseList, Course, CourseGradebook, Settings){	
	Backbone.emulateHTTP = true;
	var GradeBookRouter = Backbone.Router.extend({
		initialize: function(){
			this._views = [];	
			this.courseList = new CourseList();	
			this.gradebook_administrators = new Settings();   
			Backbone.history.start();	 	
		},
  		routes: {
    		"courses" : "courses",
	    	"gradebook/:id" :  "show-gradebook",
	    	"settings" :  "settings",
	    	"course/:cid/gradebook/add-student" : "edit-student",	    	
	    	"course/:cid/gradebook/add-student/:uid" : "edit-student"
  		},
  		initPage: function(){
			$('#wpcontent').css('padding-left', '0px');  		
  		},
  		clearViews : function(){
  			var self = this;
  			this.initPage();
		  	_.each(self._views,function(view){
		  	   view.close();
		  	});
		  	this._views = [];    			 
  		},
  		courses : function() {
  			var self = this;
			this.clearViews();			           			
			$('#wpbody-content').prepend($('#ajax-template').html());
			this.courseList.fetchCourses().then(function(val){
				$('.ajax-loader-container').remove();					
	    		var homeView = new CourseListView({collection: self.courseList});
				self._views.push(homeView);		
			});						
	  	},  
		"show-gradebook" : function(id) {
			var self = this;
			this.clearViews();            				
			this.course = new Course({id : parseInt(id)});					
			this.gradebook = new CourseGradebook({gbid: parseInt(id)});				
			$('#wpbody-content').prepend($('#ajax-template').html());							
			Promise.all([this.course.fetchCourse(),this.gradebook.fetchCourseGradebook()]).then(function(values){		
				$('.ajax-loader-container').remove();					
				var gradeBookView = new GradeBookView({gradebook: self.gradebook, course: self.course});				
				self._views.push(gradeBookView);					
			});							
  		},
		settings: function(){
			var self = this;
			this.clearViews();			           
			$('#wpbody-content').prepend($('#ajax-template').html());							
			this.gradebook_administrators.fetchSettings().then(function(val){
				$('.ajax-loader-container').remove();		
	    		var settingsPage = new SettingsPage({gradebook_administrators: self.gradebook_administrators});					
				self._views.push(settingsPage);			
			});													   
		},
		"edit-student": function(cid,uid){			
			this.clearViews();
			if(uid){
			    var editStudentView = new EditStudentView();
			} else {
			    var editStudentView = new EditStudentView();			
			}
			this._views.push(editStudentView);		
		},	
	});	  
	return GradeBookRouter;	
});