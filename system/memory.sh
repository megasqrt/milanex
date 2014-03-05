while sleep 3600; do sync; echo 3 | sudo tee /proc/sys/vm/drop_caches; done
