try:
	from setuptools import setup, find_packages
except ImportError:
	from ez_setup import use_setuptools
	use_setuptools()
	from setuptools import setup, find_packages

setup(
	name = "planster",
	version = "0.5.0",
	packages = find_packages(),
	author = "Stefan Ott",
	author_email = "stefan@ott.net",
	description = "A web-based tool to coordinate things",
	url = "http://planster.googlecode.com/",
	include_package_data = True,
	install_requires=[
		"django"
	],
	keywords="planning scheduling",
	license="GPLv3",
	classifiers=[
		"Development Status :: 5 - Production/Stable",
		"Environment :: Web Environment",
		"Framework :: Django",
		"Intended Audience :: System Administrators",
		"License :: DFSG approved",
		"License :: OSI Approved :: GNU General Public License (GPL)",
		"Natural Language :: English",
		"Operating System :: OS Independent",
		"Programming Language :: Python",
		"Topic :: Communications",
		"Topic :: Office/Business :: Scheduling",
	],
)
