<?php
/**
 * Created by PhpStorm.
 * User: nykos
 * Date: 2020/10/28
 * Time: 3:57
 */

/**
 *
 *
 *
 *
 */

// コメントアノテーションのBNF
// <Definitions> := <Definition> | <Definition> + <Lb> + <Definition> | <Primitive> | <LbRs>
// <Definition> := <Subject> + <SubjectEntry>
// <SubjectMarker> := "@"
// <Key> := /^[A-Za-z_0-9][A-Za-z_0-9\-]*/
// <Subject> := <SubjectMarker> + <Key>
// <Quote> := "\""
// <Primitive> := /^([A-Za-z0-9_\(\)\-,:. #@\`';\/\+\*=\>\<~\|\[\]\{\}\!^%$]|[^\x{01}-\x{7E}])+/
// <Number> := /^[0-9]+/
// <SubjectEntry> := <Alt-JSON left="(", right=")", joint="=>"> | <Quote> + <Primitive> + <Quote> | <Number>