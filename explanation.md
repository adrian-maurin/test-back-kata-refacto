# Explanation

 1. First step was to get familiar with the project, mainly the TemplateManager class.  
[1fdcef6](https://github.com/adrian-maurin/test-back-kata-refacto/commit/1fdcef696450ab57d0182bd5beb0a57c1a1ddea7) cleans up a bit by uniforming some statements and calls to make the readability friendlier.
 2. After running the unit tests I noticed an error, fixed it immediately in [b97870a](https://github.com/adrian-maurin/test-back-kata-refacto/commit/b97870af74b05e3057f0967b1cd6ebbfcfe1eff6).  
 There was only one unit test, so I added some in [f8f63bf](https://github.com/adrian-maurin/test-back-kata-refacto/commit/f8f63bf7def5f617de426c9068fb0d78fe91729c).   
 This was mandatory as those will be run often to ensure nothing has broke during refactorization.
 3. To ensure nothing breaks in later refactorization I added return types to functions in [9655e27](https://github.com/adrian-maurin/test-back-kata-refacto/commit/9655e271546f7a4c3135196b773970cc226619b4).
 4. First I chose to refactor the smallest part of the class, the function `getTemplateComputed`.  
    Removed the clone statement, `$tpl` is not passed as reference, it won't be modified anyway.  
    I also removed the check for `$tpl` existence because argument type is set to `Template`, absence will raise TypeError. ([0fbae93](https://github.com/adrian-maurin/test-back-kata-refacto/commit/0fbae9345c5da4aa687a50dd383516a93abd1648))
 5. Next comes the function `computeText`:
	- The combination of `if (strpos(...) !== false) { str_replace(...); }` is useless since `str_replace` won't do anything if searched string is not found in subject. So I removed them all and made as little calls to `str_replace` as possible by using arrays of search/replace strings when calling the function.
	- There were some variable duplication (removed duplicates):
		- `$destinationOfQuote` and `$destination`  where the same thing
		- `$quote` and `$_quoteFromRepository`  where the same thing
	- Some variables were misnamed:
		- `$usefulObject` has been renamed `$site`
		- `$_user` has been rename `$user`

	The function is now more streamlined and readable thanks to more coherent variables names and functions calls. ([36674ec](https://github.com/adrian-maurin/test-back-kata-refacto/commit/36674ec08e217d283e8d08bf6424e73d834a70c9))
 6. The class lacked comments to I added some docstrings to explain what input are expected and what functions ouput. ([c7674c5](https://github.com/adrian-maurin/test-back-kata-refacto/commit/c7674c529a2139994ad6338093f4ecaeaf84d8be))

