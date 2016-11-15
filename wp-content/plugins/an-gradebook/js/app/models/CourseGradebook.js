define(['backbone','models/AssignmentList','models/UserList','models/CellList'],
function(Backbone,AssignmentList,UserList,CellList){ 
	var CourseGradebook = Backbone.Model.extend({
    fetchCourseGradebook: function(){
      var self = this;       
      var promise = new Promise(function(resolve,reject){
          if(self.url()){
            self.fetch({success: function(){
                resolve(self);
              }
            });
          } else {                                       
            self.set(self.parse({
              "assignments":[
                {"id":1,"gbid":1,"assign_order":1,"assign_name":"Cal","assign_category":"HW","assign_visibility":"Students","assign_date":"2015-11-11","assign_due":"2015-11-25"}
              ],
              "cells":[
                {"id":1,"uid":2,"gbid":1,"amid":1,"assign_order":1,"assign_points_earned":100}
              ],
              "students":[
                {"first_name":"Aori","last_name":"Nevo","user_login":"anevo","id":2,"gbid":1}
              ],
                "role":"instructor"
            }));   
            resolve(self);
            
          }
      })
      return promise;
    },    
  	url: function(){      
  	 	return ajaxurl + '?action=gradebook&gbid=' + parseInt(this.get('gbid'));
  	},
  	sort_key: 'student',
  	parse: function(response){      
  		this.assignments = new AssignmentList(response.assignments);
  		this.cells = new CellList(response.cells);
  		this.students = new UserList(response.students);
  		this.sort_column = this.students;
  		this.role = response.role;
  		return response;
  	}
	});
	return CourseGradebook;
});
