'-- Remove the existing zip
del p3-profiler.zip

'-- Make a temp copy
cd ..
rmdir /S /Q .\p3-profiler
xcopy /S .\profiler-plugin .\p3-profiler\

'-- Zip it
"c:\Program Files (x86)\7-Zip\7z.exe" a -r -tzip -y -xr!?svn\* -x!.svn .\profiler-plugin\p3-profiler.zip -x!build.bat p3-profiler\*

'-- Clean up
rmdir /S /Q .\p3-profiler
