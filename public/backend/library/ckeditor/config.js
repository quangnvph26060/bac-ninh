/**
 * @license Copyright (c) 2003-2021, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function (config) {
    config.filebrowserBrowseUrl = "/laravel-filemanager?type=Files";
    config.filebrowserImageBrowseUrl = "/laravel-filemanager?type=Images";
    config.filebrowserUploadUrl = "/laravel-filemanager/upload?type=Files";
    config.filebrowserImageUploadUrl =
        "/laravel-filemanager/upload?type=Images";
};
