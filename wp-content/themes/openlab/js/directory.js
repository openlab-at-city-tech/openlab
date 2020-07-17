/*
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
*/
(function($){
		var $groupTypeCheckboxes,
			allSidebarFilters = [];

    $(document).ready(function(){
			if ( 0 !== $('.openlab-search-results').length ) {
				$('.sidebar-filter input[type="checkbox"], .custom-select select' ).each( function() {
					allSidebarFilters.push( this.id );
				} );

				$groupTypeCheckboxes = $('.sidebar-filter-checkbox input.group-type-checkbox');
				$groupTypeCheckboxes.on( 'change', calculateFilterStates );
				calculateFilterStates();
			}
    });

		calculateSelectedGroupTypes = function() {
			var allGroupTypes = [];
			var selectedGroupTypes = [];

			$groupTypeCheckboxes.each(function(){
				var thisGroupType = this;

				allGroupTypes.push( thisGroupType.value );
				if ( thisGroupType.checked ) {
					selectedGroupTypes.push( thisGroupType.value );
				}
			});

			if ( 0 === selectedGroupTypes.length ) {
				return allGroupTypes;
			} else {
				return selectedGroupTypes;
			}
		};

		/**
		 * Determines whether filters should be disabled based on select group types.
		 */
		calculateFilterStates = function() {
			var selectedGroupTypes = calculateSelectedGroupTypes();
			var disabledFilters = {};

			// Convert group-type disabled filters lists to an array of arrays (to use reduce() below).
			var disabledFiltersArray = []
			for ( var i in window.OLDirectory.groupTypeDisabledFilters ) {
				if ( -1 === selectedGroupTypes.indexOf( i ) ) {
					continue;
				}

				if ( ! window.OLDirectory.groupTypeDisabledFilters.hasOwnProperty( i ) ) {
					continue;
				}

				disabledFiltersArray.push( window.OLDirectory.groupTypeDisabledFilters[ i ] );
			}

			// Intersect of all disabled fields.
			var disabledFilters = disabledFiltersArray.reduce((a, b) => a.filter(c => b.includes(c)));

			allSidebarFilters.forEach( function( sidebarFilterId ) {
				var $el = $( '#' + sidebarFilterId );
				var $elLabel = $( 'label[for="' + sidebarFilterId + '"]' );

				// Everything is enabled by default.
				$el.removeProp( 'disabled' ).removeClass( 'disabled-checkbox' );
				$elLabel.removeClass( 'disabled-label' );

				if ( -1 !== disabledFilters.indexOf( sidebarFilterId ) ) {
					$el.prop( 'disabled', true ).addClass( 'disabled-checkbox' );
					$elLabel.addClass( 'disabled-label' );
				}
			} );
		};

}(jQuery));
