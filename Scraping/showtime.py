""" === Scraping TV Shows from Showtime ===
    @author Daniel Zurawski
"""

import scraping

def showlist(limiter=None):
    """ Return the list of information about all TV shows offered by Showtime. 
        @param limiter -- int -- The maximum number of shows to extract.
        @return [[String]]
    """
    STIME = "http://www.sho.com"
    shows = list() # List of show lists such that each show list provides show info
    soup  = scraping.brewsoup("{}/series".format(STIME)) # Get page of all shows
    alls  = soup.find(class_="section section--gradient section--pad-more") # Table of all shows
    for poster in alls.find_all(class_="promo__link"):
        if limiter and limiter <= len(shows):
            return shows # Limit number of shows for testing purposes
        url = "{0}{1}".format(STIME, poster["href"]) # Grab url of show
        shows.append(showinfo(url))
    return shows

def showinfo(url):
    """ Return show info corresponding to the show's url.
        @param url -- String -- the url of the show.
        @return [String]
        Format of returned list:
            [Name, Description, Episodes/Seasons, "ACTORS", actors...]
            Example: ["Spongebob",
                      "Square man lives on wild side."
                      "Episodes: 10, Seasons: 3",
                      "ACTORS",
                      "Will Smith",
                      "Daniel Z",
                      "Oprah Winfrey"]
    """
    info = list()
    soup = scraping.brewsoup(url)
    cast = soup.find(class_="slider slider--cast js-slider") # Find the cast/characters
    info.append(soup.find(class_="hero__headline").get_text().strip()) # Name
    info.append(soup.find("p", class_="block-container__copy").get_text().strip()) # Description
    info.append(soup.find("h3", class_="section-header section-header--border")
                .get_text().strip()) # Episodes/Seasons
    info.append("ACTORS") # To separate actors from previous information
    if cast: # Some shows did not have any cast or characters.
        for actor in cast.find_all(class_="promo__copy"):
            info.append(actor.get_text().strip()) # Add the actors
    return info

print("Starting...")
scraping.makeCSV(showlist(), "showtime.csv")
print("All Done!")