# Changelog

## [4.0.2](https://github.com/WebFiori/ui/compare/v4.0.1...v4.0.2) (2026-06-10)


### ⚠ BREAKING CHANGES

* **HTMLNode:** children() now returns Vector instead of LinkedList. Code that type-hints LinkedList for children() return must update.

### Features

* **HTMLNode:** add type-specific factory methods for template loading ([9bb0ea8](https://github.com/WebFiori/ui/commit/9bb0ea839e736cd7f9a277e8ef18ef570a02534d))
* **HTMLNode:** add type-specific factory methods for template loading ([deadb49](https://github.com/WebFiori/ui/commit/deadb49fa35dd56990e969a971fcdac63680291a)), closes [#65](https://github.com/WebFiori/ui/issues/65)
* **TemplateCompiler:** support HTMLNode objects in template slots ([a0ce21b](https://github.com/WebFiori/ui/commit/a0ce21b5275cf0a6f385ce2674167b3f21f15e40))


### Bug Fixes

* **HTMLNode:** HTML-encode & and " in attribute values in open() ([5499571](https://github.com/WebFiori/ui/commit/54995714d54aa1461de2d9956a7e9f2525e0274f)), closes [#77](https://github.com/WebFiori/ui/issues/77)
* **HTMLNode:** throw InvalidArgumentException for invalid attribute names ([9a0e728](https://github.com/WebFiori/ui/commit/9a0e7288b3ce1db9227f6554ed24409f793092b9)), closes [#76](https://github.com/WebFiori/ui/issues/76)
* **HTMLTable:** derive row/column counts from DOM children ([931b82e](https://github.com/WebFiori/ui/commit/931b82efacebaa8793d85f80857bf9332a92dea8)), closes [#71](https://github.com/WebFiori/ui/issues/71)
* resolve 10 bugs in HTMLNode, HTMLTable, and TemplateCompiler ([1da4977](https://github.com/WebFiori/ui/commit/1da4977d48e08653d94308a0571c9e16817cc741))
* **TemplateCompiler:** handle &lt;?= and &lt;? short tags in HTML text ([fea1eb5](https://github.com/WebFiori/ui/commit/fea1eb50091c9aa57fb4df498964e4721696c1fd)), closes [#60](https://github.com/WebFiori/ui/issues/60)
* **TemplateCompiler:** handle HTML comments containing &lt; characters ([cba8a81](https://github.com/WebFiori/ui/commit/cba8a81132ac580fb779ad5fb3dc806916b56ecc)), closes [#63](https://github.com/WebFiori/ui/issues/63)
* **TemplateCompiler:** isolate PHP template scope from $this ([d0e8337](https://github.com/WebFiori/ui/commit/d0e83370e5379bdfa85bb725c5979cdae51ef79d)), closes [#62](https://github.com/WebFiori/ui/issues/62)
* **TemplateCompiler:** preserve body and html element attributes ([16db386](https://github.com/WebFiori/ui/commit/16db386edf7b24ab26e8fe27f10a284936b847dd)), closes [#56](https://github.com/WebFiori/ui/issues/56)
* **TemplateCompiler:** preserve script content when tag has attributes ([4331cc6](https://github.com/WebFiori/ui/commit/4331cc6ffe9b042f58077f048429de46615a7771)), closes [#55](https://github.com/WebFiori/ui/issues/55)
* **TemplateCompiler:** prevent output buffer leak on template exception ([7325706](https://github.com/WebFiori/ui/commit/732570650435c1dc86c037122392f2926fdc441d)), closes [#59](https://github.com/WebFiori/ui/issues/59)
* **TemplateCompiler:** support UTF-8 in attribute values with &gt; char ([aaba8c0](https://github.com/WebFiori/ui/commit/aaba8c0e91cc498cd9f435f07460b74daf625f7e)), closes [#58](https://github.com/WebFiori/ui/issues/58)


### Performance Improvements

* **HTMLNode:** replace LinkedList with Vector for child node storage ([38e0a7a](https://github.com/WebFiori/ui/commit/38e0a7a1bf0419f40960e419734dc256d808d1ef)), closes [#66](https://github.com/WebFiori/ui/issues/66)


### Miscellaneous Chores

* exclude /examples from composer dist archive ([f4a5a9a](https://github.com/WebFiori/ui/commit/f4a5a9a97403450fba102a49b86bf4adbf7162b7))
* Merge pull request [#83](https://github.com/WebFiori/ui/issues/83) from WebFiori/dev ([9ba3f2a](https://github.com/WebFiori/ui/commit/9ba3f2a9f2b531ae551008663c6e428eb2594ebb))

## [4.0.1](https://github.com/WebFiori/ui/compare/v4.0.0...v4.0.1) (2026-06-01)


### Miscellaneous Chores

* align CI with ecosystem baseline ([b988017](https://github.com/WebFiori/ui/commit/b988017a0b432df49ce3a79f3fc470827da6de5f))
* align CI with ecosystem baseline ([39e81e6](https://github.com/WebFiori/ui/commit/39e81e66011acaa1081e3ce1804f75e0cb3de5b8))

## [4.0.0](https://github.com/WebFiori/ui/compare/v3.0.0...v4.0.0) (2025-10-07)


### Miscellaneous Chores

* Merge pull request [#49](https://github.com/WebFiori/ui/issues/49) from WebFiori/dev ([a833f98](https://github.com/WebFiori/ui/commit/a833f98e9d1e9c725282879de5f66e228993a765))

## [3.0.0](https://github.com/WebFiori/ui/compare/v2.6.4...v3.0.0) (2025-08-27)


### Bug Fixes

* Namespaces ([a97438b](https://github.com/WebFiori/ui/commit/a97438be75f9f88b3fddabcebdee69199cd7fee8))


### Miscellaneous Chores

* Update Readme.md ([45c376b](https://github.com/WebFiori/ui/commit/45c376b55cf5a387a83fd7d425cfc50e045e030b))
* Updated ReadMe ([9ec23a7](https://github.com/WebFiori/ui/commit/9ec23a7089381c4003719e5b353acd5bfccd6b89))

## [2.6.4](https://github.com/WebFiori/ui/compare/v2.6.3...v2.6.4) (2024-12-24)


### Miscellaneous Chores

* Updated Composer Config ([5108943](https://github.com/WebFiori/ui/commit/5108943d6b63e30c4ee990e90f66f5c2d169b5d0))
