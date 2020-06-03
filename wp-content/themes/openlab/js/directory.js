(function($){
    var $departmentSelector,
        $schoolSelector,
        deptAllOption = '<option value="all" id="dept_all">All Departments</option>';

    $(document).ready(function(){
        $departmentSelector = $('#department-select');
        $schoolSelector = $('#school-select');
        rebuildDepartmentSelector();

        $schoolSelector.on( 'change', rebuildDepartmentSelector );
    });

    rebuildDepartmentSelector = function() {
        var currentSchool = $schoolSelector.val();
        var currentDepartment = OLAcademicUnits.currentDepartment;

				if ( ! currentSchool ) {
            $departmentSelector.val('all').trigger('change');
        }

        if ( ! currentSchool || 'all' === currentSchool ) {
            $departmentSelector.prop('disabled', true);
            return;
				}


        $departmentSelector.empty();

        var nullOption = $(deptAllOption);
        if ( 0 === currentDepartment.length ) {
            nullOption.prop('selected', true);
        }
        $departmentSelector.append( nullOption );

        $.each( OLAcademicUnits.departments[ currentSchool ], function( deptSlug, deptData ) {
            var opt = $('<option value="' + deptSlug + '">' + deptData.label + '</option>');

            if ( deptSlug === currentDepartment ) {
                opt.prop('selected', true);
            }

            $departmentSelector.append(opt);
        } )

        $departmentSelector.prop('disabled', false).trigger('change');
    }
}(jQuery));
