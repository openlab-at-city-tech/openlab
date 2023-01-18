<?php
// Exit if accessed directly
defined('ABSPATH') || exit;
?>

<div class="emcs-popup-text-customizer-form">
    <div class="sc-wrapper">
        <div class="sc-container">
            <div class="row">
                <div class="col-md-8">
                    <form>
                        <div class="form-row emcs-form-row">
                            <div class="form-group col-md-6">
                                <label for="emcs-embed-type">Embed Type</label>
                                <select name="emcs-customizer-embed-type" class="form-control">
                                    <option value="emcs-inline-text">Inline</option>
                                    <option value="emcs-popup-text">Popup Text</option>
                                    <option value="emcs-popup-button">Popup Button</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="emcs-embed-text">Text</label>
                                <input type="text" class="form-control" name="emcs-embed-text" value="Book Now">
                            </div>
                        </div>
                        <div class="form-row emcs-form-row">
                            <div class="form-group col-md-6">
                                <label for="emcs-embed-text-color">Text Color</label>
                                <input type="color" class="form-control" name="emcs-embed-text-color" value="#2694ea">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="emcs-embed-text-size">Text Size(px)</label>
                                <input type="number" class="form-control" name="emcs-embed-text-size" min="10" max="30" value="12">
                            </div>
                        </div>
                        <div class="form-row emcs-form-row">
                            <div class="form-group col-md-6">
                                <label for="emcs-cookie-banner">Hide Cookie Banner</label>
                                <select name="emcs-cookie-banner" class="form-control">
                                    <option value="no">No</option>
                                    <option value="yes">Yes</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    Preview
                    <div class="emcs-customizer-preview">
                        <div class="emcs-preview-content">
                            <div class="emcs-preview"></div>
                        </div>
                    </div>
                    <input type="text" name="emcs-embed-customizer-shortcode" class="form-control" onclick="this.select();" value="">
                    <small>Click to copy shortcode</small>
                </div>
            </div>
            <button type="button" name="emcs-customizer-home" class="button button-default emcs-customizer-home">
                << Go Back </button>
        </div>
    </div>
</div>