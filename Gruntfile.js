"use strict";
 
module.exports = function (grunt) {
    // Load all grunt tasks.
    grunt.loadNpmTasks("grunt-contrib-less");
    grunt.loadNpmTasks("grunt-contrib-watch");
    grunt.loadNpmTasks("grunt-contrib-clean");
 
    grunt.initConfig({
        watch: {
            // If any .less file changes in directory "less" then run the "less" task.
            files: "less/*.less",
            tasks: ["less"]
        },
        less: {
            // Production config is also available.
            development: {
                options: {
                    // Specifies directories to scan for @import directives when parsing.
                    // Default value is the directory of the source, which is probably what you want.
                    paths: ["less/"],
                    compress: true
                },
                // This dynamically creates the list of files to be processed.
                files: [
                    {   
                        expand: true,
                        cwd: "less/",
                        src: "*.less",
                        dest: "style/",
                        ext: ".css"
                    }   
                ]
            },
        }
    });
    // The default task (running "grunt" in console).
    grunt.registerTask("default", ["less"]);
};
