rename all tables from tbg3_* -> pachno_*
update settings names:
tbg_header_name_html -> pachno_header_name_html
```
update tbg3_settings set value = 'Pachno' where name = 'b2_name' and value = 'The Bug Genie';
```

update settings values:
auth_backend: 'tbg' -> 'default'