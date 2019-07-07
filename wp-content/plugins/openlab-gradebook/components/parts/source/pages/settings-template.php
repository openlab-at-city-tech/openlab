<div id="an-gradebook-settings" class="wrap">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="wrap">

                    <h1><span><?php esc_html_e('About', 'openlab-gradebook')?></span></h1>
                    <div class="content-wrapper">

                        <h2 class="h4"><?php esc_html_e('GradeBook Assignment Grade Types:', 'openlab-gradebook')?></h2>
                        <p><?php esc_html_e('Grade type can be set per assignment by selecting "Edit" from the dropdown menu.', 'openlab-gradebook')?>
                        </p>

                        <div class="grades-table-wrapper">
                            <table id="gradesTable" class="table table-bordered table-striped">
                                <tr>
                                    <th><?php esc_html_e('Assignment Grade Type Checkmark', 'openlab-gradebook')?></th>
                                    <th><?php esc_html_e('Assignment Grade Type Letter Grades', 'openlab-gradebook')?>
                                    </th>
                                    <th><?php esc_html_e('Assignment Grade Type Numeric', 'openlab-gradebook')?></th>
                                    <th><?php esc_html_e('GradeBook Mid Value - used to compute an average from letter grades or checkmarks', 'openlab-gradebook')?>
                                    </th>
                                </tr>
                                <tr>
                                    <td rowspan="12"><i
                                            class="oplb-grdbk-icon oplb-grdbk-icon-left oplb-grdbk-check-mark-2"></i><?php esc_html_e('checked box', 'openlab-gradebook')?>
                                    </td>
                                    <td>A+</td>
                                    <td>&gt;100</td>
                                    <td>100 / 100&nbsp;<?php esc_html_e('for checked box', 'openlab-gradebook')?></td>
                                </tr>
                                <tr>
                                    <td>A<br></td>
                                    <td>93-99.9</td>
                                    <td>96</td>
                                </tr>
                                <tr>
                                    <td>A-</td>
                                    <td>90-92.9</td>
                                    <td>91.5</td>
                                </tr>
                                <tr>
                                    <td>B+</td>
                                    <td>87 - 89.9 </td>
                                    <td>88.5</td>
                                </tr>
                                <tr>
                                    <td>B</td>
                                    <td>83-86.9</td>
                                    <td>85</td>
                                </tr>
                                <tr>
                                    <td>B-</td>
                                    <td>80-82.9</td>
                                    <td>81.5</td>
                                </tr>
                                <tr>
                                    <td>C+</td>
                                    <td>77-79.9</td>
                                    <td>78.5</td>
                                </tr>
                                <tr>
                                    <td>C</td>
                                    <td>73 - 76.9</td>
                                    <td>75</td>
                                </tr>
                                <tr>
                                    <td>C-</td>
                                    <td>70 - 72.9</td>
                                    <td>71.5</td>
                                </tr>
                                <tr>
                                    <td>D+</td>
                                    <td>67- 69.9</td>
                                    <td>68.5</td>
                                </tr>
                                <tr>
                                    <td>D</td>
                                    <td>63 - 66.9</td>
                                    <td>65</td>
                                </tr>
                                <tr>
                                    <td>D-</td>
                                    <td>60-62.9</td>
                                    <td>61.5</td>
                                </tr>
                                <tr>
                                    <td><i
                                            class="oplb-grdbk-icon oplb-grdbk-icon-left oplb-grdbk-square-line"></i><?php esc_html_e('unchecked box', 'openlab-gradebook')?>
                                    </td>
                                    <td>F</td>
                                    <td>&lt;60</td>
                                    <td>50 for F / 0&nbsp;<?php esc_html_e('for unchecked box', 'openlab-gradebook')?>
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>