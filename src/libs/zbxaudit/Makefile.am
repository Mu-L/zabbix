## Process this file with automake to produce Makefile.in

noinst_LIBRARIES = libzbxaudit.a

libzbxaudit_a_SOURCES = \
	audit.h \
	audit.c \
	audit_host.c \
	audit_httptest.c \
	audit_item.c \
	audit_trigger.c \
	audit_graph.c \
	audit_ha.c

libzbxaudit_a_CFLAGS = \
	$(TLS_CFLAGS)
