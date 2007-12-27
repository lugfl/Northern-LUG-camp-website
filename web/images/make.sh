#!/bin/bash


for F in in/* ; do
	NF=$(basename $F)
	convert -scale "100x100" $F $NF
done
